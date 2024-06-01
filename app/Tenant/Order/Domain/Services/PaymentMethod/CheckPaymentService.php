<?php

namespace App\Tenant\Order\Domain\Services\PaymentMethod;

use App\Admin\Domain\Models\Admin;
use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Helpers\MyFatoorah;
use App\Infrastructure\Helpers\Payment\PaymentService;
use App\Notification\Domain\Notifications\OrderNotification;
use App\Tenant\Order\Domain\Models\Cart;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Resources\OrderResource;
use App\Shipping\GatewayFactoryInterface;
use App\Store\Domain\Models\Store;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentService extends Service
{
    public $paymentService;
    public $shippingGateway;

    public function __construct(PaymentService $paymentService, GatewayFactoryInterface $shippingGatewayFactory)
    {
        $this->paymentService = $paymentService;
        $this->shippingGateway = $shippingGatewayFactory;
    }

    public function handle($data = [])
    {
        $res = $this->paymentService->checkPayment($data);
        $lang = app()->getLocale();
        $baseUrl = $res['route'];
        if ($res['status'] != 200 || $res['paid'] != true) {
            $error = $res['msg'];
            if ($res['additional_info'] == 'web') {
                return new GenericPayload([
                    'error' => $error,
                    'route' => "$baseUrl/$lang/carts"
                ], Response::HTTP_ACCEPTED);
            } else {
                return new GenericPayload([
                    'error' => $error,
                    'route' => route('payment-error')
                ], Response::HTTP_ACCEPTED);
            }
        } else {
            $order = Order::withoutGlobalScopes()->findOrFail($res['order_id']);
            DB::beginTransaction();
            $updatedData['is_paid'] = 1;
            if (isset($res['brand'])) {
                $updatedData['payment_method_brand'] = $res['brand'];
            }
            $order->update($updatedData);
            if ($order->type == 'stores') {
                Cart::where('user_id', $order->user_id)->delete();
                $route = "$baseUrl/$lang/orders";
            } else {
                // $route = "$baseUrl/$lang/orders/services";
                $route = "$baseUrl/$lang/orders";
            }

            if ($order->wallet_payout != 0.00) {
                $order->user->transactions()->create([
                    'type' => 'pay_out',
                    'amount' => $order->wallet_payout,
                    'wallet_total' => 0.00,
                    'transactable_id' => $order->id,
                    'transactable_type' => 'App\Tenant\Order\Domain\Models\Order',
                    //'reason' => 'order',
                    'ar' => ['reasons' => "تم استخدام مبلغ  {$order->wallet_payout} من الحفظة لاستخدامها فى الطلب  رقم {$order->id}"],
                    'en' => ['reasons' => "{$order->wallet_payout} has been used from your wallet for order no. {$order->id}"],

                ]);
            }

            //shipment

            //$store->userAddress
            $store_id = $order->store_id;
            $store = Store::with('city')->find($store_id);
            if ($order->userAddress && $store) {
                info("store address", ['address' => $store->address_city, 'store' => $store]);
                try {
                    $store = Store::with('city')->find($store->id);
                    $gateway = $this->shippingGateway->create('aramex');
                    $gateway->createShipment($order, $store, $order->userAddress);
                    info("Aramex response", ['response' => $gateway]);
                } catch (\Exception $e) {
                    info("Aramex Exception", ['Exception' => $e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine()]);
                    // dd($e->getMessage());
                }
            }

            DB::commit();
            $msg = __('success.orders.sentSuccesffuly') . ' ' . $order->id;
            $admin = Admin::whereIsActive(1)->first();
            $notif_data = array(
                'ar' => ['title' => 'طلب جديد', 'body' => 'تم اضافة طلب جديد ورقم الطلب ه ' . $order->id],
                'en' => ['title' => 'new order', 'body' => 'order No.' . $order->id . 'has been added'],
            );
            $admin->notify(new OrderNotification($order, $notif_data));
            //$user->notify(new OrderNotification($order, $msg));
            send_fcm_notification(
                $admin,
                [
                    "title" => __('general.orders.newOrder'),
                    "body" => $msg,
                    "type" => 'order',
                    "model_id" => $order->id
                ],
                true
            );
            // return new GenericPayload(['message' => __('success.orders.sentSuccesffuly'). ' '. $order->id]);
//            return redirect(route('payment-success'));
            if ($res['additional_info'] == 'web') {
                return new GenericPayload(
                    [
                        'message' => $msg,
//                    'route' => 'payment-success',
                        'route' => $route,
                        'order' => new OrderResource($order),
                    ],
                    Response::HTTP_ACCEPTED
                );
            } else {
                return new GenericPayload(
                    [
                        'message' => $msg,
                        'route' => route('payment-success'),
//                        'route' => $route,
                        'order' => new OrderResource($order),
                    ],
                    Response::HTTP_ACCEPTED
                );
            }
        }
    }
}
