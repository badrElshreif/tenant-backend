<?php

namespace App\Tenant\Order\Domain\Services\Cart;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Product\Domain\Models\Product;
use App\Tenant\Order\Domain\Models\Cart;
use App\Product\Domain\Models\ProductExtraProperty;
use DB;
use Symfony\Component\HttpFoundation\Response;
class AddToCartService extends Service
{
    public function handle($data = [])
    {
        // return new GenericPayload(auth()->user(), 422 );
        try {
            if(auth()->check()){
                $user = auth()->user();
                $data['user_id'] = $user->id;
            }else{
                $data['user_id'] = null;
            }

            // Begin Transaction
            //DB::beginTransaction();
            $product = Product::findOrFail($data['product_id']);

            $cart = Cart::where([
                ['device_id',$data['device_id']??""],
                ['product_id',$data['product_id']]
            ])->orWhere([
                ['user_id',auth()->id()],
                ['product_id',$data['product_id']]
            ])->when(isset($data['warranty_id']), function($collection) use ($data){
                return $collection->where('warranty_id', $data['warranty_id']);
            })->first();

            //$cart = $user->cart->where('product_id', $data['product_id'])->first();
            /*if(auth()->check()){
                $cart = $user->cart->where('product_id', $data['product_id'])
                ->when(isset($data['warranty_id']), function($collection) use ($data){
                    return $collection->where('warranty_id', $data['warranty_id']);
                })
                ->first();
            }else{
                $cart = Cart::whereDeviceId($data['device_id'])->whereNull('user_id')->where('product_id', $data['product_id'])
                ->when(isset($data['warranty_id']), function($collection) use ($data){
                    return $collection->where('warranty_id', $data['warranty_id']);
                })
                ->first();
            }*/
            $qty_check = $this->checkQuantity($data['quantity'], $product, $cart);
            if($qty_check != null){
                //DB::rollback();
                return new GenericPayload($qty_check, 422
                );
            }

            // $store_check = $this->checkStore($product);
            // if($store_check != null)
            //     return new GenericPayload($store_check, 422
            //     );
            $data['store_id'] = $product->store_id;
            if(auth()->check())
            $data['user_id'] =auth()->id();
            if(!$cart){
                $cart = Cart::create($data);
            }else{
                $data['quantity'] = $cart->quantity + $data['quantity'];
                $cart->update($data);
            }

                // Commit Transaction
                //DB::commit();
                return new GenericPayload($cart, Response::HTTP_CREATED);
        } catch (\PDOException $ex){
            // Rollback Transaction
            //DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        } catch (Exception $ex) {
            // Rollback Transaction
            //DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }

    public function checkQuantity($qty, $product, $cart = null){

        $cart_product_qty = $cart ? $cart->quantity : 0;

        if($product->quantity < 1){
            return optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailable');
        }

        if(($cart_product_qty + $qty) > $product->max_purchase_quantity){
            return optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.max_purchase_no');
        }

        if(($cart_product_qty + $qty) > $product->quantity){
            return optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailableQuantity');
        }

        return null;
    }

    public function checkStore($product){
        if(auth()->check()){
            $cart = auth()->user()->cart()->orderBy('id', 'desc')->first();
        }else{
            $cart = Cart::whereDeviceId(request()->device_id)->whereNull('user_id')->orderBy('id', 'desc')->first();
        }
        if($cart && $cart->product->store_id != $product->store_id)
            return __('error.differentStoreInCart');
        return null;
    }
}
