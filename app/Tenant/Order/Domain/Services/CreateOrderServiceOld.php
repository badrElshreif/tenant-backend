<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Resources\OrderResource;
use App\Tenant\Order\Domain\Models\Status;
use App\Tenant\Order\Domain\Models\OrderItem;
use App\PromoCode\Domain\Models\PromoCode;
use App\Tenant\Order\Domain\Models\PaymentMethod;
use App\Store\Domain\Models\Store;
use App\Product\Domain\Models\ProductView;
use App\Warranty\Domain\Models\Warranty;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\User\Domain\Models\Customer;
use DB;
use App\Notification\Domain\Notifications\OrderNotification;
use App\Tenant\Order\Domain\Models\BankTransfer;
use Illuminate\Support\Arr;
use App\Admin\Domain\Models\Admin;
use App\Tenant\Order\Domain\Models\Transaction;
use Symfony\Component\HttpFoundation\Response;

class CreateOrderServiceOld extends Service
{
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            if(isset($data['user_id']) && !auth('api')->check())
                $user = Customer::findOrFail($data['user_id']);
            else
                $user = auth()->user();

            if(count($data['items']) > 0){
                $data['user_id'] = $user->id;
                $data['status_id'] = status('order', 'new');
                //$payment_method = paymentMethod::findOrFail($data['payment_method_id']);
                $store = Store::findOrFail($data['store_id']);
                $data['type'] = 'stores';
                $order_calculations = $this->getOrderSubtotal($data['items']);
                $data['subtotal'] = $order_calculations['subtotal'];
                $data['offer_discount'] = $order_calculations['offer_discount'];
                $data['warranties_amount'] = $order_calculations['warranties_amount'];
                $data['delivery_charge'] = $store->has_delivery_service == 1 ? $store->delivery_charge : setting('delivery_charge');
                $data['total'] = $data['subtotal'] + $data['delivery_charge'] + $data['warranties_amount'] - $data['offer_discount'];

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
                $order_min_cost = setting('order_min_cost') ? : 1000;
                if($order_min_cost > $data['total'])
                    return new GenericPayload(__('error.orders.orderMinCost'). $order_min_cost , 422);

                $order_added_tax = setting('order_added_tax');
                if($order_added_tax)
                    $data['order_added_tax'] = $data['total'] * $order_added_tax / 100;
                else
                    $data['order_added_tax'] = 0.000;
                $data['total'] += $data['order_added_tax'];

                $application_dues_percentage = $store->application_dues > 0 ? $store->application_dues : $setting('application_dues');
                if($application_dues_percentage)
                    $data['application_dues'] = $data['total'] * $application_dues_percentage / 100;

                $data['remain'] = $data['total'];
                if(isset($data['use_wallet']) && $data['use_wallet'] == 1){
                    $wallet = optional($user->transactions()->orderBy('id', 'desc')->first())->wallet_total;
                    if(!$wallet)
                        $wallet = 0.00;
                    if($wallet && $wallet > 0){
                        if($wallet > $data['total']){
                            $data['wallet_payout'] = $data['total'];
                            $data['remain'] = 0.00;
                            $data['wallet_total'] = $wallet - $data['total'];
                        }else{
                            $data['wallet_payout'] = $wallet;
                            $data['remain'] = $data['total'] - $wallet;
                            $data['wallet_total'] = 0.00;
                        }
                    }

                    if($data['remain'] > 0 && !isset($data['payment_method_id']))
                        return new GenericPayload(__('error.orders.requiredPaymentMethod'), 422);
                    if(isset($data['payment_method_id'])){
                        $payment_method = paymentMethod::findOrFail($data['payment_method_id']);
                        if($payment_method->type == 'wallet' && $data['remain'] > 0)
                            return new GenericPayload(__('error.orders.walletNotEnough'), 422);
                    }

                    if($data['remain'] == 0.00){
                        $payment_method = paymentMethod::where('type', 'wallet')->firstOrFail();
                        $data['payment_method_id'] = $payment_method->id;
                    }
                } else {
                    if(!isset($data['payment_method_id']))
                        return new GenericPayload(__('error.orders.requiredPaymentMethod'), 422);
                    $payment_method = paymentMethod::findOrFail($data['payment_method_id']);
                }

                $order = Order::create($data);
                $order_items = $this->handleOrderItems($order, $data['items']);
                if($order_items != null)
                    return new GenericPayload($order_items, 422);

                if(isset($data['use_wallet']) && $data['use_wallet'] == 1 && $wallet > 0){
                    $user->transactions()->create([
                        'type' => 'pay_out',
                        'amount' => $data['wallet_payout'],
                        'wallet_total' => $data['wallet_total'],
                        'transactable_id' => $order->id,
                        'transactable_type' => 'App\Tenant\Order\Domain\Models\Order',
                        //'reason' => 'order',
                        'ar'  => ['reasons' => "تم استخدام مبلغ  {$order->wallet_payout} من الحفظة لاستخدامها فى الطلب  رقم {$order->id}"],
                        'en'  => ['reasons' => "{$order->wallet_payout} has been used from your wallet for order no. {$order->id}"],

                    ]);
                }
                if($payment_method->type == 'bank_transfer'){
                    if(!isset($data['depositor_name']) || !isset($data['deposit_amount']) || !isset($data['deposit_receipt'])){
                        DB::rollback();
                        return new GenericPayload(__('error.orders.requiredBankTransferData'), 422);
                    }
                    if($data['deposit_amount'] < $order->remain){
                        DB::rollback();
                        return new GenericPayload(__('error.orders.lowTransferedMoney'), 422);
                    }
                    $transfer = $order->bankTransfer()->create(Arr::only($data, ['depositor_name', 'deposit_amount', 'deposit_receipt']));
                }

                auth()->user()->orderStatuses()->firstOrCreate([
                    'order_id' => $order->id,
                    'status_id' => $order->status_id,
                    'type' => 'order',
                    'reason' => isset($data['reason']) ? $data['reason'] : null
                ]);
                // Commit Transaction
                DB::commit();

                $msg = __('success.orders.sentSuccesffuly'). ' '. $order->id;
                $admin = Admin::whereIsActive(1)->first();
                $notif_data = array(
                    'ar' => ['title' => 'طلب جديد', 'body' => 'تم اضافة طلب جديد ورقم الطلب ه '. $order->id],
                    'en' => ['title' => 'new order', 'body' => 'order No.'.$order->id. 'has been added'],
                );
                $admin->notify(new OrderNotification($order, $notif_data));
                //$user->notify(new OrderNotification($order, $msg));
                send_fcm_notification(
                    $admin,
                    [
                        "title" => __('general.orders.newOrder'),
                        "body" => $msg,
                        "type" => 'order',
                        "model_id" =>  $order->id
                    ],
                    true
                );
                // return new GenericPayload(['message' => __('success.orders.sentSuccesffuly'). ' '. $order->id]);
                return new GenericPayload(
                    [
                        'message' => $msg,
                        'order' => new OrderResource($order),
                    ],
                    Response::HTTP_RESET_CONTENT
                );
            }

            return new GenericPayload(__('error.orders.emptyCart'), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\Illuminate\Database\QueryException $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        } catch (\PDOException $ex){
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        }
        catch (\Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );

        }
    }

    private function getDiscount($product){
        $discount = 0.00;
        $free_product = null;
        $offer = $product->offer->first();
        if ($offer){
            if ($offer->type == "free_product"){
                $free_product = $offer->free_product_id;
            }else if ($offer->type == 'percentage'){
                $discount = $product->price_including_tax * $offer->value / 100;
            }else{
                $discount = $offer->value;
            }
        }
        return array ('id' => optional($offer)->id, 'value' => $discount, 'free_product' => $free_product);
    }


    private function getCouponDiscount($coupon, $total = 0.000){
        $discount = 0.00;
        //$discount_percent = 0;
        if ($coupon->type == "value"){
            $discount = $total > 0 ? ($coupon->value <= $total ? $coupon->value : $total) : 0.00;
            //$discount_percent = $total > 0 ? ($coupon->value <= $total ? round(($coupon->value/$total)*100, 3) : 100) : 0;

        }else if ($coupon->type == 'percentage'){
            $discount = $total * $coupon->value / 100;
            //$discount_percent = $coupon->value;
        }else{
            $discount = 0.00;
            //$discount_percent = 0;
        }
        return $discount;
    }

    private function getOrderSubtotal($items){
        $subtotal = 0.00;
        $offer_discount = 0.00;
        $warranties_amount = 0.000;
        foreach ($items as $item) {
            $product = ProductView::findOrFail($item['product_id']);
            //$subtotal +=  $item->quantity *($product->price_including_tax - $this->getDiscount($product)['value']);
            $subtotal +=  $item['quantity'] * $product->price_including_tax;
            $offer_discount +=  $item['quantity'] * $this->getDiscount($product)['value'];
            if(isset($item['warranty_id'])){
                $warranty = Warranty::findOrFail($item['warranty_id']);
                $warranties_amount += $item['quantity'] * $warranty->price;
            }
        }
        return ['subtotal' => $subtotal, 'offer_discount' => $offer_discount, 'warranties_amount' => $warranties_amount];
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

    private function checkQuantity($qty, $product){
        if($product->quantity <= 0){
            return optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailable');
        }
        if($product->quantity < $qty){
            return optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailableQuantity');
        }
        return null;
    }

    private function handleOrderItems($order, $items) {
        $err_msg = null;
        //check and save order items
        foreach ($items as $item) {
            $product = ProductView::findOrFail($item['product_id']);
            $qty_check = $this->checkQuantity($item['quantity'], $product);
            if($qty_check != null)
                return $qty_check;

            if(isset($item['warranty_id'])){
                $warranty = Warranty::findOrFail($item['warranty_id']);
                $item['warranty_price'] = $warranty->price;
            }else {
                $item['warranty_id'] = null;
                $item['warranty_price'] = 0.000;
            }

            $discount = $this->getDiscount($product);
            $order_item = $order->orderItems->where('product_id', $item['product_id'])->first();
            if(!$order_item){
                $order_item = $order->orderItems()->create([
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'product_price'       => $product->price_including_tax,
                    'offer_discount'    => $discount['value'],
                    'free_product_id' => $discount['free_product'] ? : null,
                    'offer_id' => $discount['id'],
                    'warranty_id' => $item['warranty_id'],
                    'warranty_price' => $item['warranty_price']
                ]);
            }else{
                $order_item->update([
                    'quantity ' => $order_item->quantity + $item['quantity']
                ]);
            }

        }
        return null;
    }
}
