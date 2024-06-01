<?php

namespace App\Tenant\Order\Domain\Services\Cart;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Product\Domain\Models\Product;
use App\Tenant\Order\Domain\Models\Cart;
use App\Product\Domain\Models\ProductExtraProperty;
use DB;
use App\User\Domain\Models\User;
use App\Infrastructure\Exceptions\UserNotFoundException;
use App\Infrastructure\Exceptions\QueryException;
use Symfony\Component\HttpFoundation\Response;

class AddToUserCartService extends Service
{
    public function handle($data = [])
    {
        try {
            $user = User::findOrFail($data['user_id']);
            // Begin Transaction
            //DB::beginTransaction();
            if(isset($data['items']) && count($data['items']) . 0){
                foreach ($data['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    if($item['quantity'] > $product->max_purchase_quantity){
                        return new GenericPayload(
                            optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.max_purchase_no'), 422
                        );
                    }
                    if($product->quantity <= 0){
                        return new GenericPayload(
                            optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailable'), 422
                        );
                    }
                    if($item['quantity'] > $product->quantity){
                        return new GenericPayload(
                            optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailableQuantity'), 422
                        );
                    }
                    if(isset($item['extra_properties']) && count($item['extra_properties']) > 0){
                        //validate extra properties
                        $extra_prop_validation = $this->validateExtraProperties($item['extra_properties'], $product);
                        if($extra_prop_validation != null){
                            //DB::rollback();
                            return new GenericPayload($extra_prop_validation, 422
                            );
                        }

                        //retrieve cart
                        $cart = $this->checkCartExistence($item['extra_properties'], $product->id);
                        if(!$cart){
                            $cart = $user->cart()->create($item);
                            $this->handleExtraProperties($item['extra_properties'], $cart);
                        }else{

                            // $cart->update([
                            //     'quantity ' => $item['quantity'] + $cart->quantity
                            // ]);
                            $cart->quantity = $item['quantity'] + $cart->quantity;
                            //$cart->quantity = $item['quantity'];
                            $cart->save();

                        }
                    }else {
                        $property_has_options_check = ProductExtraProperty::whereProductId($product->id)->whereHas('property', function($property){
                            $property->where('has_options', 1);
                        })->first();
                        if($property_has_options_check){
                            // Rollback Transaction
                            //DB::rollback();
                            return new GenericPayload(
                                optional($product->translate(app()->getLocale()))->name.' : '. __('error.cart.requiredProperty'), 422
                            );
                        }

                        $cart = $user->cart->where('product_id', $item['product_id'])->first();
                        if(!$cart){
                            $cart = $user->cart()->create($item);
                        }else{
                            // $cart->update([
                            //     'quantity ' => $cart->quantity + $item['quantity']
                            // ]);
                            $cart->quantity = $item['quantity'] + $cart->quantity;
                            //$cart->quantity = $item['quantity'];
                            $cart->save();
                        }
                    }
                }
                //$user->cart()->createMany($data['items']);
                $user_cart = $user->cart();
                $total = 0.00;
                foreach ($user_cart->get() as $item) {
                    $total = $total + ($item->quantity *$this->getDiscount($item->product));
                }
                // Commit Transaction
                //DB::commit();
                $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
                return new GenericPayload(['total' => number_format((float)$total, 2, '.', ''), 'cart' => $user_cart->paginate($limit)], 201);

            }else{
                $product = Product::findOrFail($data['product_id']);
                if($data['quantity'] > $product->max_purchase_quantity){
                    return new GenericPayload(
                        optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.max_purchase_no'), 422
                    );
                }
                if($product->quantity <= 0){
                    return new GenericPayload(
                        optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailable'), 422
                    );
                }
                if($data['quantity'] > $product->quantity){
                    return new GenericPayload(
                        optional($product->translate(app()->getLocale()))->name.' : '. __('error.orders.unAvailableQuantity'), 422
                    );
                }
                //$cart = $user->cart->where('product_id', $data['product_id'])->first();

                if(isset($data['extra_properties'])){
                    //validate extra properties
                    $extra_prop_validation = $this->validateExtraProperties($data['extra_properties'], $product);
                    if($extra_prop_validation != null){
                        //DB::rollback();
                        return new GenericPayload($extra_prop_validation, 422
                        );
                    }

                    //retrieve cart
                    $cart = $this->checkCartExistence($data['extra_properties'], $product->id);
                    if(!$cart){
                        $cart = Cart::create($data);
                        $this->handleExtraProperties($data['extra_properties'], $cart);
                    }else{
                        // $cart->update([
                        //     'quantity ' => $$cart->quantity + $data['quantity']
                        // ]);
                        $cart->quantity = $data['quantity'] + $cart->quantity;
                        $cart->save();
                    }

                }else {
                    $property_has_options_check = ProductExtraProperty::whereProductId($product->id)->whereHas('property', function($property){
                            $property->where('has_options', 1);
                        })->first();

                    if($property_has_options_check){
                            // Rollback Transaction
                        //DB::rollback();
                        return new GenericPayload(
                            optional($product->translate(app()->getLocale()))->name.' : '. __('error.cart.requiredProperty'), 422
                        );
                    }
                    $cart = Cart::whereUserId($data['user_id'])->whereNull('user_id')->first();
                    if(!$cart){
                        $cart = Cart::create($data);
                    }else{
                        // $cart->update([
                        //     'quantity ' => $cart->quantity + $data['quantity']
                        // ]);
                        $cart->quantity = $data['quantity'] + $cart->quantity;
                        $cart->save();
                    }
                }
                // Commit Transaction
                //DB::commit();
                return new GenericPayload($cart);
            }
        } catch (\PDOException $ex){
            // Rollback Transaction
            //DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        }catch (Exception $ex) {
            // Rollback Transaction
            //DB::rollback();
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

    function validateExtraProperties($extra_properties, $product){
        $required_product_properties = $product->properties()->whereHas('property', function($q) {
                $q->where('has_options', 1);
            })->pluck('property_id')->toArray();
        $ids  = array_column($extra_properties, 'property_id');
        if(count(array_intersect($required_product_properties, $ids))!= count($required_product_properties)){
            return __('error.cart.missedProperty');
        }
        foreach($extra_properties as $extra_property){
            $check_prop = $product->properties()->where('property_id', $extra_property['property_id'])->first();
            if(!$check_prop){
                return optional($product->translate(app()->getLocale()))->name.' : '. __('error.cart.invalidProperty');
            }

            $property = $check_prop->property->first();
            if($property->has_options != 1){
                return optional($property->translate(app()->getLocale()))->name.' : '. __('error.cart.noOptions');
            }
            if($property->has_options == 1 && !isset($extra_property['property_option_id'])){
                return optional($property->translate(app()->getLocale()))->name.' : '. __('error.cart.requiredOption');
            }

            $option_check = $check_prop->options()->where('property_option_id', $extra_property['property_option_id'])->first();
            if(!$option_check){
                return optional($property->translate(app()->getLocale()))->name.' : '. __('error.cart.invalidPropertyOption');
            }
        }
        return null;
    }

    function handleExtraProperties($extra_properties, $cart){
        foreach($extra_properties as $extra_property){
            $cart->extraProperties()->create([
                'property_id' => $extra_property['property_id'],
                'property_option_id' => $extra_property['property_option_id'],
            ]);
        }
        return null;
    }

    function checkCartExistence($extra_properties, $product_id){
        $cart_check = null;
            $query = Cart::whereUserId(request()->user_id)->where('product_id', $product_id);
            if(count($extra_properties) > 0){
                foreach ($extra_properties as $extra_property) {
                    $query->whereHas('extraProperties', function($q) use ($extra_property){
                        $q->where(
                            [
                                ['property_id' , $extra_property['property_id']],
                                ['property_option_id' , $extra_property['property_option_id']],
                            ]
                        );
                    });
                }
            }else{

            }
            $cart_check = $query->first();

        return $cart_check;
    }
}
