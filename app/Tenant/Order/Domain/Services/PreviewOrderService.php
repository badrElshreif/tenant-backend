<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Models\Status;
use App\Tenant\Order\Domain\Models\OrderItem;
use App\Tenant\Order\Domain\Resources\CartResource;
use App\Tenant\Order\Domain\Models\Cart;
use App\PromoCode\Domain\Models\PromoCode;
use App\Product\Domain\Models\Product;
use App\Product\Domain\Models\ProductView;
use App\Product\Domain\Models\ProductExtraProperty;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\User\Domain\Models\Customer;
use DB;
use App\Notifications\OrderNotification;
use App\Tenant\Order\Domain\Models\BankTransfer;
use Illuminate\Support\Arr;
use App\Tenant\Order\Domain\Resources\OrderMobileResource;
use App\Tenant\AppContent\Domain\Models\Setting;
use App\Admin\Domain\Models\Admin;
use App\Tenant\Order\Domain\Models\Transaction;
use Symfony\Component\HttpFoundation\Response;

class PreviewOrderService extends Service
{
    public function handle($data = [])
    {
        try {
            $user = auth()->user();
            if(auth()->check()){
                $cart = Cart::where('user_id',auth()->id())->get();
            }else{
                 $cart = Cart::where('device_id',request()->device_id)->get();
            }

            $data['wallet'] = optional($user->transactions()->orderBy('id', 'desc')->first())->wallet_total ? : 0.00;
            $data['remain'] = 0.00;
            $data['wallet_payout'] = 0.00;
            if($data['type'] == 'stores' && count($cart) > 0){
                $store = optional($user->cart()->first())->store;
                $order_calculations = $this->getOrderSubtotal($cart);
                $data['subtotal'] = $order_calculations['subtotal'];
                $data['offer_discount'] = $order_calculations['offer_discount'];
                $data['warranties_amount'] = $order_calculations['warranties_amount'];
                $data['delivery_charge'] = $store->has_delivery_service == 1 ? $store->delivery_charge : setting('delivery_charge');
                $data['total'] = $data['subtotal'] + $data['delivery_charge'] + $data['warranties_amount'] - $data['offer_discount'];

                $data['promo_code_discount'] = 0.000;
                $data['promo_code_type'] = '';
                if(isset($data['promo_code'])){
                    $promoCodeData = $this->validateCoupon($data['promo_code'], $data['total'], $store->id, $user->id);
                    if($promoCodeData['msg'] != null)
                        return new GenericPayload($promoCodeData['msg'], 422);
                    $coupon = $promoCodeData['promo_code'];
                    $data['promo_code_id'] = $promoCodeData['promo_code']->id;
                    $data['promo_code_discount'] = $promoCodeData['coupon_discount'];
                    $data['promo_code_type'] = $promoCodeData['promo_code']->type;
                    if($promoCodeData['promo_code']->type == 'free_delivery_charge')
                        $data['delivery_charge'] = 0.000;
                }

                $data['total'] = $data['subtotal'] + $data['delivery_charge'] + $data['warranties_amount'] - $data['offer_discount'] - $data['promo_code_discount'];
                //$data['total'] -= $data['promo_code_discount'];
                $order_min_cost = setting('order_min_cost') ? : 0.00;
                if($order_min_cost > $data['total'])
                    return new GenericPayload(__('error.orders.orderMinCost'). $order_min_cost , 422);

                $order_added_tax = setting('order_added_tax')?: 0.00;
                if($order_added_tax)
                    $data['order_added_tax'] = $data['total'] * $order_added_tax / 100;
                else
                    $data['order_added_tax'] = 0.000;
                $data['total'] += $data['order_added_tax'];

                $application_dues_percentage = $store->application_dues ? : $setting('application_dues');
                if($application_dues_percentage)
                    $data['application_dues'] = $data['total'] * $application_dues_percentage / 100;


                if(isset($data['use_wallet']) && $data['use_wallet'] == 1){
                    $wallet = optional($user->transactions()->orderBy('id', 'desc')->first())->wallet_total;
                    $data['remain'] = $data['total'];
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
                }

                return new GenericPayload([
                    'subtotal' => number_format((float)$data['subtotal'], 2, '.', ''),
                    'offer_discount' => number_format((float)$data['offer_discount'], 2, '.', ''),
                    'warranties_amount' => number_format((float)$data['warranties_amount'], 2, '.', ''),
                    'delivery_charge' => number_format((float)$data['delivery_charge'], 2, '.', ''),
                    'promo_code_discount' => number_format((float)$data['promo_code_discount'], 2, '.', ''),
                    'promo_code_type' => $data['promo_code_type'],
                    'total' => number_format((float)$data['total'], 2, '.', ''),
                    'order_added_tax' => number_format((float)$data['order_added_tax'], 2, '.', ''),
                    'wallet' => number_format((float)$data['wallet'], 2, '.', ''),
                    'wallet_payout' => number_format((float)$data['wallet_payout'], 2, '.', ''),
                    'remain' => number_format((float)$data['remain'], 2, '.', ''),
                ], Response::HTTP_RESET_CONTENT);
            }else if($data['type'] == 'centers'){
                $service = ProductView::findOrFail($data['service_id']);
                if($service->category->type != 'centers')
                    return new GenericPayload(__('error.orders.serviceIsRequired'), 422);
                $data['subtotal'] = $service->preview_fees;
                $data['order_added_tax'] = $data['subtotal'] * setting('order_added_tax') / 100;
                $data['total'] = $data['subtotal'] + $data['order_added_tax'];

                if(isset($data['use_wallet']) && $data['use_wallet'] == 1){
                    $wallet = optional($user->transactions()->orderBy('id', 'desc')->first())->wallet_total;
                    $data['remain'] = $data['total'];
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
                }

                return new GenericPayload([
                    'subtotal' => number_format((float)$data['subtotal'], 2, '.', ''),
                    'total' => number_format((float)$data['total'], 2, '.', ''),
                    'order_added_tax' => number_format((float)$data['order_added_tax'], 2, '.', ''),
                    'wallet' => number_format((float)$data['wallet'], 2, '.', ''),
                    'wallet_payout' => number_format((float)$data['wallet_payout'], 2, '.', ''),
                    'remain' => number_format((float)$data['remain'], 2, '.', ''),
                ], Response::HTTP_RESET_CONTENT);
            } else {
                return new GenericPayload(__('error.orders.emptyCart'), 422);
            }

            return new GenericPayload(__('error.orders.emptyCart'), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
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
            $subtotal = $subtotal + ($item->quantity *optional($item->product)->price_including_tax);
            $offer_discount = $offer_discount + ($item->quantity *$this->getDiscount($item->product)['value']);

            if($item->warranty)
                $warranties_amount = $warranties_amount + ($item->quantity *$item->warranty->price);
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
}
