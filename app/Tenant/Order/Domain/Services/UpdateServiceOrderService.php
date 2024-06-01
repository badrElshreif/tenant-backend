<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Models\Status;
use App\Product\Domain\Models\ProductView;
use App\Product\Domain\Models\Product;
use App\Product\Domain\Models\ProductExtraProperty;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\User\Domain\Models\User;
use App\Warranty\Domain\Models\Warranty;
use App\PromoCode\Domain\Models\PromoCode;
use DB;
use App\Notification\Domain\Notifications\OrderNotification;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class UpdateServiceOrderService extends Service
{
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            // if(isset($data['user_id']) && !auth('api')->check())
            //     $user = User::findOrFail($data['user_id']);
            // else
            //     $user = auth()->user();
            $order = Order::findOrFail($data['order_id']);
            $data['subtotal'] = $order->subtotal;
            $data['total'] = $order->total;
            if(isset($data['service_id'])){
                $service = ProductView::findOrFail($data['service_id']);
                if($service->category->type != 'centers')
                    return new GenericPayload(__('error.orders.serviceIsRequired'), 422);
                $data['type'] = 'centers';
                $data['subtotal'] = $service->preview_fees;
                $data['order_added_tax'] = $data['subtotal'] * setting('order_added_tax') / 100;
                $data['total'] = $data['subtotal'] + $data['order_added_tax'];

                $order_items = $this->handleOrderItems($order, $service, Arr::only($data, ['subcategory_id', 'problem_description']));
                if($order_items != null)
                    return new GenericPayload($order_items, 422);
            }

            $data['promo_code_discount'] = 0.000;

            if(isset($data['promo_code'])){
                $promoCodeData = $this->validateCoupon($data['promo_code'], $data['total'], $store->id, $user->id);
                if($promoCodeData['msg'] != null)
                    return new GenericPayload($promoCodeData['msg'], 422);
                $coupon = $promoCodeData['promo_code'];
                $data['promo_code_id'] = $promoCodeData['promo_code']->id;
                $data['promo_code_discount'] = $promoCodeData['coupon_discount'];
                if($promoCodeData['promo_code']->type == 'free_delivery_charge')
                    $data['delivery_charge'] = 0.000;
            }
            $data['total'] -= $data['promo_code_discount'];

            $data['application_dues_percentage'] = $store->application_dues > 0 ? $store->application_dues : $setting('application_dues');
            if($data['application_dues_percentage'])
                $data['application_dues'] = $data['total'] * $data['application_dues_percentage'] / 100;



            // if($order->payment_method->type != 'bank_transfer' && $payment_method->type == 'bank_transfer'){
            //     if(!isset($data['depositor_name']) || !isset($data['deposit_amount']) || !isset($data['deposit_receipt'])){
            //         DB::rollback();
            //         return new GenericPayload(__('error.orders.requiredBankTransferData'), 422);
            //     }
            //     if($data['deposit_amount'] < $data['total']){
            //         DB::rollback();
            //         return new GenericPayload(__('error.orders.lowTransferedMoney'), 422);
            //     }
            //     $transfer = $order->bankTransfer()->create(Arr::only($data, ['depositor_name', 'deposit_amount', 'deposit_receipt']));
            // }
            $order->update($data);


            // Commit Transaction
            DB::commit();
            $order = Order::find($order->id);
            return new GenericPayload($order, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }

    private function handleOrderItems($order, $service, $data) {
        $err_msg = null;
        //check and save order items
        $order_item = $order->orderService()->create([
            'service_id'  => $service->id,
            'service_price' => $service->price,
            'preview_fees' => $service->preview_fees,
            'subcategory_id' => $data['subcategory_id'],
            'problem_description' => $data['problem_description']
        ]);

        $order->orderService()->updateOrCreate(
                [
                    'service_id' => $service->id
                ],
                [
                    'service_id'  => $service->id,
                    'service_price' => $service->price,
                    'preview_fees' => $service->preview_fees,
                    'subcategory_id' => $data['subcategory_id'],
                    'problem_description' => $data['problem_description']
                ]
            );
        return null;
    }

    private function validateCoupon($promo_code, $total, $store_id, $user_id) {
        $msg = null;
        $promo_code_obj = null;
        $promo_code_discount = 0.000;
        $promo_code_obj = PromoCode::where([
            ['code', $promo_code],
            ['is_active', 1],
            ['start_date', '<=', date('Y-m-d')],
            ['end_date', '>=', date('Y-m-d')]
        ])
        ->whereRelation('stores', 'stores.id', $store_id)
        // ->whereHas('stores', function ($query) use($store_id){
        //     $query->where('stores.id', $store_id);
        // })
        ->first();
        if(!$promo_code_obj)
            return ['msg' => __('error.coupons.invalidCoupon')];

        if($promo_code_obj->applied_to == 'specific_user' && $promo_code_obj->user_id != $user_id)
            return ['msg' => __('error.coupons.invalidCoupon')];

        $previous_orders_count = $promo_code_obj->orders->where('promo_code_id', $promo_code_obj->id)->count();
        if($previous_orders_count >= $promo_code_obj->max_use_number)
            $msg = __('error.coupons.maxUsageNo');
        if($total < $promo_code_obj->order_min_cost)
            $msg = __('error.coupons.lowOrderCost');

        return [
            'promo_code' => $promo_code_obj,
            'coupon_discount' => $this->getCouponDiscount($promo_code_obj, $total),
            'msg' => $msg
        ];

    }

}
