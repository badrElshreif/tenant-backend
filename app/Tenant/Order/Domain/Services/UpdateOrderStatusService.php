<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Models\Status;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Notification\Domain\Notifications\OrderNotification;
use App\Product\Domain\Models\Product;
use App\User\Domain\Models\User;
use App\Admin\Domain\Models\Admin;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class UpdateOrderStatusService extends Service
{
    private $msg, $notif_data ;
    public function handle($data = [])
    {
        try {
            $order = Order::findOrFail($data['order_id']);
            $order_old_status = $order->status->key;
            //$status = Status::findOrFail($data['status_id']);
            $status = Status::whereType('order')->where('key', $data['status'])->firstOrFail();
            $items = [];
            $data['reason'] = isset($data['reason']) ? $data['reason'] : null;

            $check_status = $this->checkStatus($order, $status, $data);
            if($check_status)
                return new GenericPayload($check_status, 422);


            if($status->key == 'accepted'){

                foreach ($order->orderItems()->get() as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    if($item['quantity'] > $product->max_purchase_quantity){
                        return new GenericPayload(
                            optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.max_purchase_no'), 422
                        );
                    }

                    if($product->quantity <= 0 || ($item['quantity'] > $product->quantity)){
                        return new GenericPayload(
                            optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailableQuantity'), 422
                        );
                    }
                }
            }else if($status->key == 'rejected' || $status->key == 'canceled'){
                if($order->wallet_payout > 0){
                    $wallet = optional($order->user->transactions()->orderBy('id', 'desc')->first())->wallet_total ? : 0.00;
                    $order->user->transactions()->create([
                        'type' => 'pay_in',
                        'amount' => $order->wallet_payout,
                        'wallet_total' => $wallet + $order->wallet_payout,
                        'transactable_id' => $order->id,
                        'transactable_type' => 'App\Tenant\Order\Domain\Models\Order',
                        //'reason' => 'refund',
                        'ar'  => ['reason' => "تم إضافة مبلغ  {$order->wallet_payout} بسبب رفض الطلب رقم {$order->id}"],
                        'en'  => ['reason' => "{$order->wallet_payout} has been added to wallet as order no. {$order->id} has been rejected"],
                    ]);
                }
            }

            $order->update([
                'status_id' => $status->id,
                'reason' => isset($data['reason']) ? $data['reason'] : null
            ]);

            $order = Order::findOrFail($data['order_id']);

            $this->handleOrderMessages($order, $status, $data['reason']);
            if($order->type == 'stores')
                $this->handleOrderItems($order, $status, $order_old_status);



           //$order = Order::findOrFail($data['order_id']);
            $order_type = $order->type == 'stores' ? 'order' : 'service_order';

            auth()->user()->orderStatuses()->firstOrCreate([
                'order_id' => $order->id,
                'status_id' => $order->status_id,
                'type' => 'order',
                'reason' => isset($data['reason']) ? $data['reason'] : null
            ]);


            if($order->status->key == 'delivered'){
                $admin = Admin::whereIsActive(1)->first();
                $admin->notify(new OrderNotification($order, $this->notif_data));
            }else{
                $order->user->notify(new OrderNotification($order, $this->notif_data));
            }
            send_fcm_notification(
                $order->user,
                [
                    "title" => __("general.order_statuses.{$status->key}"),
                    "body" => $this->msg,
                    "type" => $order_type,
                    "model_id" =>  $order->id
                ]
            );

            return new GenericPayload($order, Response::HTTP_CREATED);


        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            dd($ex);
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }

    private function checkStatus($order, $status, $data){
        if($order->type == 'stores'){
            if($status->key == 'rejected' || $status->key == 'accepted' || $status->key == 'canceled'){
            if($order->status->key != 'new')
                return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'ready_for_delivery'){
                if($order->status->key != 'accepted')
                    return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'delivering'){
                if($order->status->key != 'ready_for_delivery')
                    return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'delivered'){
                if($order->status->key != 'delivering')
                    return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'rejected' || $status->key == 'canceled'){
                if(!isset($data['reason']))
                    return __('error.orders.requiredReason');
            }
        }else {
            if(!in_array($status->key, ['rejected', 'accepted', 'canceled']))
                return __('error.wrongStatus');
            if($status->key == 'rejected' || $status->key == 'accepted' || $status->key == 'canceled'){
            if($order->status->key != 'new')
                return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'delivered'){
                if($order->status->key != 'accepted')
                    return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'rejected' || $status->key == 'canceled'){
                if(!isset($data['reason']))
                    return __('error.orders.requiredReason');
            }
        }
        return null;
    }

    private function handleOrderMessages($order, $status, $reason=null){

        switch ($status->key) {
            case 'rejected':
                $this->msg = __("success.orders.rejectedOrder"). ' '.$reason;
                $this->notif_data = array(
                    'ar' => ['title' => 'طلب مرفوض', 'body' => 'تم رفض طلبك ورقم الطلب هو '.$order->id. ' والسبب هو '.$reason],
                    'en' => ['title' => 'Your order is rejected', 'body' => "Your Order {$order->id} is rejected and the reason is {$reason}"],
                );
                break;
            case 'accepted':
                $this->msg = __('success.orders.acceptedOrder'). ' '.$order->id;
                $this->notif_data = array(
                    'ar' => ['title' => 'قبول الطلب', 'body' => 'تم قبول طلبك ورقم الطلب هو '.$order->id],
                    'en' => ['title' => 'Your order is accepted', 'body' => "Your Order {$order->id} is accepted"],
                );
                break;
            case 'delivered':
                $this->msg = __('success.orders.deliveredOrder'). ' '.$order->id;
                $this->notif_data = array(
                    'ar' => ['title' => 'وصول الطلب', 'body' => 'تم وصول الطلب رقم  '.$order->id],
                    'en' => ['title' => 'delivered order', 'body' => 'the order '. $order->id. 'has been delivered'],
                );
                break;
            case 'ready_for_delivery':
                $this->msg = __('success.orders.ready_for_delivery'). ' '.$order->id;
                $this->notif_data = array(
                    'ar' => ['title' => 'جاهز على التوصيل', 'body' => 'الطلب رقم '.$order->id. 'جاهز على التوصيل'],
                    'en' => ['title' => 'Check your order Status', 'body' => "your Order {$order->id}  is ready for delivery"],
                );
                break;
            case 'delivering':
                $this->notif_data = array(
                    'ar' => ['title' => 'جارى توصيل الطلب', 'body' => 'جارى توصيل الطلب رقم '.$order->id],
                    'en' => ['title' => 'Be ready .. your order is coming', 'body' => "Your order {$order->id} Delivery in progress"],
                );
                $this->msg = __('success.orders.deliveringOrder'). ' '.$order->id;
                break;
            case 'canceled':
                $this->notif_data = array(
                    'ar' => ['title' => 'الغاء الطلب', 'body' => 'تم الغاء الطلب رقم '.$order->id. ' والسبب هو '.$reason],
                    'en' => ['title' => 'canceled order', 'body' => 'Your order '. $order->id. 'has been canceled and the reason is '.$reason],
                );
                $this->msg = __('success.orders.canceledOrder'). ' '.$reason;
                break;

            default:
                $this->notif_data = '';
                $this->msg = __('success.orders.updatedSuccessfully');
                break;
        }
    }

    private function handleOrderItems($order, $status, $order_old_status){
        if($status->key == 'accepted' || $status->key == 'canceled'){
            foreach ($order->orderItems()->get() as $item) {
                $product = Product::findOrFail($item->product_id);
                if($status->key == 'canceled'){
                    if($order_old_status != 'new')
                        $product->update([
                            'quantity' => ($product->quantity + $item->quantity)
                        ]);
                }else{
                    $product->update([
                        'quantity' => ($product->quantity - $item->quantity)
                    ]);
                }
                if($item->free_product_id !== null){
                    $free_product = Product::findOrFail($item->free_product_id);
                    if($status->key == 'canceled'){
                        if($order_old_status != 'new')
                            $free_product->update([
                                'quantity' => ($free_product->quantity + $item->quantity)
                            ]);
                    }else{
                        $free_product->update([
                            'quantity' => ($free_product->quantity - $item->quantity)
                        ]);
                    }
                }
            }
        }
        return null;
    }

}
