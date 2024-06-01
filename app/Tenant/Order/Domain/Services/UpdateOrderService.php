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
use App\Infrastructure\Helpers\Traits\UploaderHelper;
use App\Store\Domain\Models\Store;
use App\User\Domain\Models\User;
use App\Warranty\Domain\Models\Warranty;
use App\PromoCode\Domain\Models\PromoCode;
use DB;
use App\Notification\Domain\Notifications\OrderNotification;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use MPDF;

class UpdateOrderService extends Service
{
    use UploaderHelper;

    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            // if(isset($data['user_id']) && !auth('api')->check())
            //     $user = User::findOrFail($data['user_id']);
            // else
            //     $user = auth()->user();
            $store=Store::findOrFail($data['store_id']);
            $order = Order::findOrFail($data['order_id']);
            $subtotal = 0.00;
            $data['subtotal'] = $order->subtotal;
            $data['total'] = $order->total;
            if(isset($data['items'])){
                $order_items = $this->handleOrderItems($order, $data['items']);
                if($order_items['err_msg'])
                    return new GenericPayload( $order_items, 422);
                $data['subtotal'] = $order_items['subtotal'];
                $data['offer_discount'] = $order_items['offer_discount'];
                $data['warranties_amount'] = $order_items['warranties_amount'];
                $data['total'] = $data['subtotal'] + $order->delivery_charge + $data['warranties_amount'] - $data['offer_discount'];
                $order_added_tax = setting('order_added_tax');
                if($order_added_tax)
                    $data['order_added_tax'] = $data['total'] * $order_added_tax / 100;
                else
                    $data['order_added_tax'] = 0.000;

                $data['total'] += $data['order_added_tax'];
            }

            $data['promo_code_discount'] = 0.000;

            if(isset($data['promo_code'])){
                $promoCodeData = $this->validateCoupon($data['promo_code'], $data['total'], $data['store_id'], $order->user_id);
                if($promoCodeData['msg'] != null)
                    return new GenericPayload($promoCodeData['msg'], 422);
                $coupon = $promoCodeData['promo_code'];
                $data['promo_code_id'] = $promoCodeData['promo_code']->id;
                $data['promo_code_discount'] = $promoCodeData['coupon_discount'];
                if($promoCodeData['promo_code']->type == 'free_delivery_charge')
                    $data['delivery_charge'] = 0.000;
            }
            $data['total'] -= $data['promo_code_discount'];

            $data['application_dues_percentage'] = $store->application_dues > 0 ? $store->application_dues : setting('application_dues');
            if($data['application_dues_percentage'])
                $data['application_dues'] = $data['total'] * $data['application_dues_percentage'] / 100;

            if($order->store_has_delivery_service != 1)
                    $data['application_dues'] = $data['application_dues'] + setting('delivery_charge');



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
            // remove old invoice file
            $this->deleteFile($order->invoice_file, 'invoices');
            $data['invoice_file'] = time().'_'. Str::random(4).'.pdf';
            $order->update($data);
            //$order->update(Arr::only($data, ['subtotal', 'total', 'admin_notes']));
            $order = Order::with('orderItems', 'store', 'user', 'userAddress')->find($order->id);
            // load invoice pdf file
            MPDF::loadView('bill', ['order' => $order], [], [])->save(storage_path("app/public/invoices/".$data['invoice_file']));

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

    private function handleOrderItems($order, $items){
        $warranties_amount = 0.000;
        $offer_discount = 0.000;
        $subtotal = 0.000;
        $err_msg = null;

        $order_items_ids = array_column($items, 'product_id');
        //dd($order_items_ids);
        foreach($order->orderItems()->get() as $order_item){
            if(! in_array($order_item->product_id, $order_items_ids)){
                $item_product = Product::findOrFail($order_item->product_id);
                if(in_array($order->status->key, ['accepted', 'ready_for_delivery', 'delivering']))
                    $item_product->update([
                        'quantity' => $item_product->quantity + $order_item->quantity
                    ]);
                $order_item->delete();
            }
        }
        foreach ($items as $item) {
            $product = ProductView::findOrFail($item['product_id']);
            if(isset($item['warranty_id'])){
                $warranty = Warranty::findOrFail($item['warranty_id']);
                $item['warranty_price'] = $warranty->price;
            }else {
                $item['warranty_id'] = null;
                $item['warranty_price'] = 0.000;
            }
            if($item['quantity'] > $product->max_purchase_quantity){
                $err_msg = optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.max_purchase_no');
            }
            if($product->quantity <= 0){
                $err_msg = optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailable');
            }
            if($item['quantity'] > $product->quantity){
                $err_msg = optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailableQuantity');
            }
            $orderItem = $order->orderItems()->where('product_id', $item['product_id'])->first();
            if($orderItem){
                if($orderItem->quantity != $item['quantity']){
                    $orderItem->update([
                        'quantity' => $item['quantity'],
                        'warranty_id' => $item['warranty_id'],
                        'warranty_price' => $item['warranty_price']
                    ]);

                    if(in_array($order->status->key, ['accepted', 'ready_for_delivery', 'delivering'])){
                        $product_quantity = $product->quantity + $orderItem->quantity - $item['quantity'];
                        $product->update([
                            'quantity' => $product_quantity
                        ]);
                    }
                }
            } else{
                $discount = $this->getDiscount($product);
                $orderItem = $order->orderItems()->create([
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'product_price'       => $product->price_including_tax,
                    'offer_discount'    => $discount['value'],
                    'free_product_id' => $discount['free_product'] ? : null,
                    'offer_id' => $discount['id'],
                    'warranty_id' => $item['warranty_id'],
                    'warranty_price' => $item['warranty_price']
                ]);
            }
            $subtotal +=  $item['quantity'] * $product->price_including_tax;
            $offer_discount += $item['quantity'] * $product->discount;
            $warranties_amount += $item['quantity'] * $item['warranty_price'];
        }

        return array ('err_msg' => $err_msg, 'subtotal' => $subtotal, 'offer_discount' => $offer_discount, 'warranties_amount' => $warranties_amount);

    }

}
