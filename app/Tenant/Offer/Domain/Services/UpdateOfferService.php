<?php

namespace App\Tenant\Offer\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Offer\Domain\Models\Offer;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Product\Domain\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class UpdateOfferService extends Service
{
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            $offer = Offer::findOrFail($data['offer_id']);
            // if(isset($data['is_active']) && $data['is_active'] == 0){
            // 	if(count($offer->products()->get()) > 0)
            // 		return new GenericPayload(
            //              __('error.cannotDeactivate'), 422
            //         );
            // }

            $products_ids = isset($data['products']) ? $data['products'] : [];
            if(isset($data['start_date']) && isset($data['end_date'])){
                $checkOffer = Offer::where('id', '!=', $data['offer_id'])
                    ->where([
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
            }

            if(isset($data['type']) && $data['type'] == 'value' && isset($data['products']) && count($data['products']) > 0){
                $value_validation = $this->validateValue($data['products'], $data['value']);

                if($value_validation != null){
                    return new GenericPayload($value_validation, 422
                    );
                }

            }

            $offer->update($data);
            if(isset($data['products']))
                $offer->products()->sync($data['products']);
        // Commit Transaction
            DB::commit();
            return new GenericPayload($offer, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\Illuminate\Database\QueryException $ex) {
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
