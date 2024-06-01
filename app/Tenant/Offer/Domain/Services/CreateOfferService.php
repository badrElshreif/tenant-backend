<?php

namespace App\Tenant\Offer\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Offer\Domain\Models\Offer;
use Illuminate\Support\Facades\DB;
use App\Product\Domain\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class CreateOfferService extends Service
{
    public function handle($data = [])
    {
        try {
            $products_ids = $data['products'];
            $checkOffer = Offer::where([
                    ['start_date', '<=', $data['start_date'] ],
                    ['end_date', '>=', $data['end_date'] ]
                ])
                ->whereHas('products', function ($q) use ( $products_ids ) {
                    $q->whereIn('product_id', $products_ids);
                })
                ->first();
            if($checkOffer)
                return new GenericPayload(
                    __('error.offerCheck'), 422
                );

            $data['is_active'] = isset($data['is_active']) ? isset($data['is_active']) : 1;

            if($data['type'] == 'value' && isset($data['products']) && count($data['products']) > 0){
                $value_validation = $this->validateValue($data['products'], $data['value']);

                if($value_validation != null){
                    return new GenericPayload($value_validation, 422
                    );
                }

            }

            if(auth()->guard('store')->check())
                $data['store_id'] = auth()->user()->store_id;
                        // Begin Transaction
            DB::beginTransaction();
	        $offer = Offer::create($data);
	        if(isset($data['products']))
	        	$offer->products()->sync($data['products'], false);
	        // Commit Transaction
	        DB::commit();
	        return new GenericPayload($offer, Response::HTTP_CREATED);

	    }catch (\Illuminate\Database\QueryException $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (\PDOException $ex){
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (\Exception $ex) {
        	// Rollback Transaction
            DB::rollback();
            return new GenericPayload($ex->getMessage(), 422);
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }

    }

    function validateValue($products, $value){
        foreach($products as $product_id){
            $product = Product::find($product_id);
            if(!$product)
                continue;
            if($product->price < $value){
                return __('error.invalidOfferValue').  optional($product->translate(app()->getLocale()))->name;
            }
        }
        return null;
    }
}
