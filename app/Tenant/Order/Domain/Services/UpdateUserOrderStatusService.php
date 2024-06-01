<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Models\Status;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Notification\Domain\Notifications\OrderNotification;
use App\Product\Domain\Models\Product;
use App\Admin\Domain\Models\Admin;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserOrderStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $order = Order::findOrFail($data['order_id']);
            $status = Status::whereType('order')->where('key', $data['status'])->firstOrFail();

            $check_status = $this->checkStatus($order, $status, $data);
            if($check_status)
                return new GenericPayload($check_status, 422);

            $order->update([
                'status_id' => $status->id,
                'reason' => isset($data['reason']) ? $data['reason'] : null
            ]);

            if($status->key =='canceled'){
                if($order->wallet_payout > 0){
                    $wallet = optional($order->user->transactions()->orderBy('id', 'desc')->first())->wallet_total ? : 0.00;
                    $order->user->transactions()->create([
                        'type' => 'pay_in',
                        'amount' => $order->wallet_payout,
                        'wallet_total' => $wallet + $order->wallet_payout,
                        'transactable_id' => $order->id,
                        'transactable_type' => 'App\Tenant\Order\Domain\Models\Order',
                        //'reason' => 'refund',
                        'ar'  => ['reason' => "تم إضافة مبلغ  {$order->wallet_payout} بسبب إلغاء الطلب رقم {$order->id}"],
                        'en'  => ['reason' => "{$order->wallet_payout} has been added to wallet as order no. {$order->id} has been canceled"],
                    ]);
                }
            }

            switch ($status->key) {
                case 'delivered':
                    $msg = __('success.orders.deliveredOrder'). ' '.$order->id;
                    $notif_data = array(
                        'ar' => ['title' => 'وصول الطلب', 'body' => 'تم وصول الطلب رقم  '.$order->id],
                        'en' => ['title' => 'delivered order', 'body' => 'the order no. '. $order->id. 'has been delivered'],
                    );
                    break;
                case 'canceled':
                    $msg = __('success.orders.canceledOrder'). ' '.$order->reason;
                    $notif_data = array(
                        'ar' => ['title' => 'إلغاء الطلب', 'body' => 'تم إلغاء الطلب رقم  '.$order->id],
                        'en' => ['title' => 'canceled order', 'body' => 'the order no. '. $order->id. 'has been canceled'],
                    );
                    break;

                default:
                    $msg = __('success.orders.updatedSuccessfully');
                    $notif_data = array(
                        'ar' => ['title' => __('success.orders.updatedSuccessfully', [], 'ar'), 'body' => __('success.orders.updatedSuccessfully', [], 'ar')],
                        'en' => ['title' => __('success.orders.updatedSuccessfully', [], 'en'), 'body' => __('success.orders.updatedSuccessfully', [], 'en')],
                    );
                    break;
            }
            $order = Order::findOrFail($data['order_id']);
            // if($order->status->key == 'canceled' && $order->type == 'stores'){
            //     foreach ($order->orderItems()->get() as $item) {
            //         $product = Product::findOrFail($item->product_id);
            //         $product->update([
            //             'quantity' => ($product->quantity + $item->quantity)
            //         ]);
            //         if($product->free_product_id !== null){
            //             $free_product = Product::findOrFail($item->free_product_id);
            //             $free_product->update([
            //                 'quantity' => ($free_product->quantity + $item->quantity)
            //             ]);
            //         }
            //     }
            // }
            auth()->user()->orderStatuses()->firstOrCreate([
                'order_id' => $order->id,
                'status_id' => $order->status_id,
                'type' => 'order',
                'reason' => isset($data['reason']) ? $data['reason'] : null
            ]);

            if(auth()->id() != $order->user->id){
                //send notification to order owner

                $order->user->notify(new OrderNotification($order, $notif_data));
                send_fcm_notification(
                    $order->user,
                    [
                        "title" => __("general.order_statuses.{$status->key}"),
                        "body" => $msg,
                        "type" => 'order',
                        "model_id" =>  $order->id
                    ]
                );
            }
            $admin = Admin::whereIsActive(1)->first();
            $admin->notify(new OrderNotification($order, $notif_data));
            return new GenericPayload($order, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }

    private function checkStatus($order, $status, $data){
        if($order->type == 'stores'){
            if(!in_array($status->key, ['canceled', 'delivered']))
                return __('error.wrongStatus');
            if($status->key == 'canceled' && $order->status->key != 'new'){
                return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'delivered'){
                if($order->status->key != 'delivering')
                    return __('error.orders.cannotChangeStatus');
            }
            if($status->key == 'canceled'){
                if(!isset($data['reason']))
                    return __('error.orders.requiredReason');
            }
        }else {
            if(!in_array($status->key, ['canceled', 'delivered']))
                return __('error.wrongStatus');
            if($status->key == 'canceled' && $order->status->key != 'new')
                return __('error.orders.cannotChangeStatus');

            if($status->key == 'delivered' && $order->status->key != 'accepted')
                return __('error.orders.cannotChangeStatus');

            if($status->key == 'canceled'){
                if(!isset($data['reason']))
                    return __('error.orders.requiredReason');
            }
        }
        return null;
    }
}
