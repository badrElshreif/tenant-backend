<?php

namespace App\Tenant\Order\Domain\Services\Cart;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Cart;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\Order\Domain\Resources\CartResource;
class UpdateCartService extends Service
{
    public function handle($data = [])
    {
        try {
            $cart = Cart::where([
                ['device_id',$data['device_id']],
                ['id',$data['id']]
            ])->orWhere([
                ['user_id',auth()->id()],
                ['id',$data['id']]
            ])->first();
            if(!$cart)
                return new GenericPayload(
                __('error.notFound'), 422
            );

            $qty_check = $this->checkQuantity($data['quantity'], $cart);

            if($qty_check != null)
                return new GenericPayload($qty_check, 422);


            $cart->update($data);

            //return new GenericPayload($cart, Response::HTTP_CREATED);

            $cartList = Cart::where([
                ['device_id',$data['device_id']],
                ['id',$data['id']]
            ])->orWhere([
                ['user_id',auth()->id()],
                ['id',$data['id']]
            ])->get();
            $total = 0.00;
            $warranties_total = 0.00;

            foreach ($cartList as $item) {
                // $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $item->created_at);
                // $diff_in_days = $to->diffInDays($from);
                // if($diff_in_days > $cart_period){
                //     $item->delete();
                // }else{
                //     $total = $total + ($item->quantity *$this->getDiscount($item->product));
                // }
                //dd($item->product);
                $total = $total + ($item->quantity *$this->getDiscount($item->product));
                if($item->warranty)
                    $warranties_total = $warranties_total + ($item->quantity *$item->warranty->price);
            }
            // foreach ($cart->get() as $item) {
            //     $total = $total + ($item->quantity *$this->getDiscount($item->product));
            // }

            return new GenericPayload([
                'subtotal' => number_format((float)$total, 2, '.', ''),
                'warranties_total' => number_format((float)$warranties_total, 2, '.', ''),
                'cart' => new CartResource($cart)
            ], Response::HTTP_RESET_CONTENT);

        } catch (\PDOException $ex){
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }

    private function getDiscount($product){
        if($product){
            $discount = 0.00;
            $free_product = null;
            $offer = $product->offer->first();
            if ($offer){
                if ($offer->type == "free_product"){
                    $free_product = $offer->freeProduct;
                }else if ($offer->type == 'percentage'){
                    $discount = $product->price_including_tax * $offer->value / 100;
                }else{
                    $discount = $offer->value;
                }
            }
            return $product->price_including_tax - $discount;
        }
        return 0;
    }

    public function checkQuantity($qty, $cart = null){

        //$cart_product_qty = $cart ? $cart->quantity : 0;
        $cart_product_qty = 0;

        if($cart->product->quantity < 1){
            return optional($cart->product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailable');
        }

        if(($cart_product_qty + $qty) > $cart->product->max_purchase_quantity){
            return optional($cart->product->translate(app()->getLocale()))->name.' : '. __('error.orders.max_purchase_no');
        }

        if(($cart_product_qty + $qty) > $cart->product->quantity){
            return optional($cart->product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailableQuantity');
        }

        return null;
    }


}
