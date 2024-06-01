<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Models\Status;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Notification\Domain\Notifications\OrderNotification;
use App\Product\Domain\Models\Product;
use App\Shipping\GatewayFactoryInterface;
use App\User\Domain\Models\User;
use App\Admin\Domain\Models\Admin;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Utils;
use GuzzleHttp\Exception\ClientException;

class UpdateOrdersStatusService extends Service
{
    protected $token;
    protected $shippingGateway;

    public function __construct(GatewayFactoryInterface $shippingGatewayFactory)
    {
        $this->token = null;
        $this->shippingGateway = $shippingGatewayFactory;
    }

    public function handle($data = [])
    {
        // Begin Transaction
        DB::beginTransaction();
        try {
            $status = Status::whereType('order')->where('key', $data['status'])->firstOrFail();
            if ($status->key == 'rejected') {
                if (!isset($data['reason']))
                    return new GenericPayload(__('error.orders.requiredReason'), 422);
            }
            if (isset($data['orders'])) {
                foreach ($data['orders'] as $order_id) {
                    $order = Order::findOrFail($order_id);
                    if ($status->key == 'rejected' || $status->key == 'accepted') {
                        if ($order->status->key != 'new')
                            return new GenericPayload(__('error.orders.cannotChangeStatus'), 422);
                    }

                    if ($status->key == 'canceled') {
                        if ($order->status->key == 'delivered' || $order->status->key == 'rejected')
                            return new GenericPayload(__('error.orders.cannotChangeStatus'), 422);
                    }
                    if ($status->key == 'delivered') {
                        if ($order->status->key != 'delivering')
                            return new GenericPayload(__('error.orders.cannotChangeStatus'), 422);
                    }

                    $order_old_status = $order->status->key;
                    if ($status->key == 'accepted') {
                        $return = $this->handleAcceptedOrder($order);

                        if ($return) {
                            // Rollback Transaction
                            DB::rollback();
                            return new GenericPayload($return, 422);
                        }
                    } else if ($status->key == 'delivering') {

                       /* $store = $order->store;
                        if (null !== $store->address_city && null !== $order->deliveryOptionId) {
                            try {
                                $gateway = $this->shippingGateway->create('aramex');
                                $gateway->createShipment($order, $store, $order->userAddress);
                            } catch (ClientException $e) {
                                $response = $e->getResponse();
                                $jsonBody = (string)$response->getBody();
                                $errorCode = Utils::jsonDecode($jsonBody)->errorCode;

                                if ($errorCode != 4) {
                                    return new GenericPayload(
                                        $e->getMessage(), 422
                                    );
                                }
                            }
                        } else {
                            return new GenericPayload(__('error.orders.cannotChangeDeliveringStatus'), 422);
                        } */
                    } else if ($status->key == 'rejected' || $status->key == 'canceled') {
                        $return = $this->handleRejectedOrder($order);
                    }
                    $order->update([
                        'status_id' => $status->id,
                        'reason' => isset($data['reason']) ? $data['reason'] : null
                    ]);

                    $this->handleProductsQty($order_id, $order_old_status);

                    auth()->user()->orderStatuses()->firstOrCreate([
                        'order_id' => $order->id,
                        'status_id' => $order->status_id,
                        'type' => 'order',
                        'reason' => isset($data['reason']) ? $data['reason'] : null
                    ]);

                    $this->handleNotifications($order, $status);
                }
            }
            // Commit Transaction
            DB::commit();
            return new GenericPayload(['message' => __('success.updatedSuccessfuly')]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            dd($ex);
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }

    public function handleGift($order)
    {
        if ($order->gift) {
            $gift_user = User::find($order->gift->user_id);
            if ($gift_user) {
                $gift_msg = __('general.orders.orderGiftRequested') . $order->user->name;
                $notif_data = array(
                    'ar' => ['title' => 'هدية', 'body' => 'تم طلب هدية لك من  ' . $order->user->name . ' ورقم الطلب هو ' . $order->id],
                    'en' => ['title' => 'Gift', 'body' => 'you have a gift from ' . $order->user->name . 'and the order no. is' . $order->id],
                );
                send_fcm_notification(
                    $gift_user,
                    [
                        "title" => __('general.orders.gift'),
                        "body" => $gift_msg,
                        "type" => 'order',
                        "model_id" => $order->id
                    ]
                );

                $gift_user->notify(new OrderNotification($order, $notif_data));
            }
        }
    }

    public function handleAcceptedOrder($order)
    {
        $items = [];
        foreach ($order->orderItems()->get() as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($item['quantity'] > $product->max_purchase_quantity) {
                return optional($product->translate(app()->getLocale()))->name . ' : ' . __('error.orders.max_purchase_no');
            }
            if ($product->quantity <= 0 || ($item['quantity'] > $product->quantity)) {
                return optional($product->translate(app()->getLocale()))->name . ' : ' . __('error.orders.unAvailableQuantity');
            }
            $obj = new \stdClass();
            $obj->refid = $product->refId;
            $obj->barcode = $product->barcode;
            $obj->quan = intVal($item['quantity']);
            $obj->price = floatVal($product->price_including_tax);
            array_push($items, $obj);
            if ($item->free_product_id !== null) {
                $free_product = Product::findOrFail($item->free_product_id);
                if ($free_product->quantity <= 0 || ($item['quantity'] > $free_product->quantity)) {
                    return optional($free_product->translate(app()->getLocale()))->name . ' : ' . __('error.orders.unAvailableQuantity');
                }
                $obj = new \stdClass();
                $obj->refid = $free_product->refId;
                $obj->barcode = $free_product->barcode;
                $obj->quan = intVal($item['quantity']);
                $obj->price = floatVal($free_product->price_including_tax);
                array_push($items, $obj);
            }
        }
        if (count($items) > 0)
            $this->addOrder($order, $items);

        return null;
    }

    public function handleRejectedOrder($order)
    {
        if ($order->wallet_payout > 0) {
            $wallet = optional($order->user->transactions()->orderBy('id', 'desc')->first())->wallet_total ?: 0.00;
            $order->user->transactions()->create([
                'type' => 'pay_in',
                'amount' => $order->wallet_payout,
                'wallet_total' => $wallet + $order->wallet_payout,
                'transactable_id' => $order->id,
                'transactable_type' => 'App\Tenant\Order\Domain\Models\Order',
                //'reason' => 'refund',
                'ar' => ['reason' => "تم إضافة مبلغ  {$order->wallet_payout} بسبب رفض الطلب رقم {$order->id}"],
                'en' => ['reason' => "{$order->wallet_payout} has been added to wallet as order no. {$order->id} has been rejected"],
            ]);
        }
        return null;
    }

    public function handleProductsQty($order_id, $order_old_status)
    {
        $order = Order::findOrFail($order_id);
        if ($order->status->key == 'accepted' || $order->status->key == 'canceled') {
            foreach ($order->orderItems()->get() as $item) {
                $product = Product::findOrFail($item->product_id);
                if ($order->status->key == 'canceled') {
                    if ($order_old_status != 'new')
                        $product->update([
                            'quantity' => ($product->quantity + $item->quantity)
                        ]);
                } else {
                    $product->update([
                        'quantity' => ($product->quantity - $item->quantity)
                    ]);
                }
                if ($item->free_product_id !== null) {
                    $free_product = Product::findOrFail($item->free_product_id);
                    if ($order->status->key == 'canceled') {
                        if ($order_old_status != 'new')
                            $free_product->update([
                                'quantity' => ($free_product->quantity + $item->quantity)
                            ]);
                    } else {
                        $free_product->update([
                            'quantity' => ($free_product->quantity - $item->quantity)
                        ]);
                    }
                }
            }
        }

        return null;
    }

    public function handleNotifications($order, $status)
    {
        switch ($status->key) {
            case 'rejected':
                $msg = __("success.orders.rejectedOrder") . ' ' . $order->reason;
                $notif_data = array(
                    'ar' => ['title' => 'طلب مرفوض', 'body' => 'تم رفض طلبك ورقم الطلب هو ' . $order->id],
                    'en' => ['title' => 'rejected order', 'body' => 'your order has been rejected and the order no. is ' . $order->id],
                );
                break;
            case 'accepted':
                $msg = __('success.orders.acceptedOrder') . ' ' . $order->id;
                $notif_data = array(
                    'ar' => ['title' => 'قبول الطلب', 'body' => 'تم قبول طلبك ورقم الطلب هو ' . $order->id],
                    'en' => ['title' => 'accepted order', 'body' => 'your order has been accepted and the order no. is ' . $order->id],
                );
                $this->handleGift($order);
                break;
            case 'delivered':
                $msg = __('success.orders.deliveredOrder') . ' ' . $order->id;
                $notif_data = array(
                    'ar' => ['title' => 'وصول الطلب', 'body' => 'تم وصول الطلب رقم  ' . $order->id],
                    'en' => ['title' => 'delivered order', 'body' => 'the order no. ' . $order->id . 'has been delivered'],
                );
                break;
            case 'ready_for_delivery':
                $msg = __('success.orders.ready_for_delivery') . ' ' . $order->id;
                $notif_data = array(
                    'ar' => ['title' => 'جاهز على التوصيل', 'body' => 'الطلب رقم ' . $order->id . 'جاهز على التوصيل'],
                    'en' => ['title' => 'ready for delivery order', 'body' => 'the order no. ' . $order->id . 'is ready for delivery'],
                );
                break;
            case 'delivering':
                $notif_data = array(
                    'ar' => ['title' => 'جارى توصيل الطلب', 'body' => 'جارى توصيل الطلب رقم ' . $order->id],
                    'en' => ['title' => 'Delivery in progress', 'body' => 'Delivery of order no. ' . $order->id . 'is in progress'],
                );
                $msg = __('success.orders.deliveringOrder') . ' ' . $order->id;
                break;
            case 'canceled':
                $notif_data = array(
                    'ar' => ['title' => 'الغاء الطلب', 'body' => 'تم الغاء الطلب رقم ' . $order->id],
                    'en' => ['title' => 'canceled order', 'body' => 'the order no. ' . $order->id . 'has been canceled'],
                );
                $msg = __('success.orders.canceledOrder') . ' ' . $order->reason;
                break;

            default:
                $msg = __('success.orders.updatedSuccessfully');
                break;
        }

        if ($order->status->key == 'delivered') {
            $admin = Admin::whereIsActive(1)->first();
            $admin->notify(new OrderNotification($order, $notif_data));
        } else {
            $order->user->notify(new OrderNotification($order, $notif_data));
        }
        send_fcm_notification(
            $order->user,
            [
                "title" => __("general.order_statuses.{$status->key}"),
                "body" => $msg,
                "type" => 'order',
                "model_id" => $order->id
            ]
        );

        return null;
    }

    public function addOrder($order, $items)
    {
        $client = new Client();
        try {
            if ($this->token == null) {
                $auth_response = $client->post(
                    config('app.waseet_base_url') . 'api/Auth/authenticate',
                    array(
                        'headers' => [
                            'Authorization' => config('app.foodics_authorization'),
                            'Accept' => "application/json",
                            'Content-Type' => "application/json",
                            'Access-Control-Allow-Origin' => '*',
                            'Access-Control-Allow-Credentials' => 'true'
                        ],
                        'json' => [
                            "username" => "was",
                            "password" => "Was@8300025"
                        ]
                    )
                );
                $data = json_decode($auth_response->getBody());
                $this->token = "Bearer ${data}";
            }

            if ($this->token) {
                $response = $client->post(
                    config('app.waseet_base_url') . 'BasApi/SaleInv',
                    array(
                        'headers' => [
                            'Authorization' => $this->token,
                            'Accept' => "application/json",
                            'Content-Type' => "application/json",
                            'Access-Control-Allow-Origin' => '*',
                            'Access-Control-Allow-Credentials' => 'true'
                        ],
                        'json' => [
                            "orderid" => $order->id,
                            "customername" => optional($order->user)->name,
                            "vendorid" => 1,
                            "coupon_discount" => floatVal($order->coupon_discount),
                            "delivery_charge" => floatVal($order->delivery_charge),
                            "items" => $items
                        ]
                    )
                );
                $order_data = json_decode($response->getBody());
            }

            return true;
        } catch (GuzzleException $ex) {
            //dd($ex);
        }
    }
}
