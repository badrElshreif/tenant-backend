<?php

namespace App\Tenant\Product\Domain\Services\Rating;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Resources\RatingResource;
use Symfony\Component\HttpFoundation\Response;
class UpdateRateService extends Service
{
    public function handle($data = [])
    {
        try {
            $rating = auth()->user()->ratings->where('product_id', $data['product_id'])->first();
            if(!$rating)
                return new GenericPayload(
                __('error.notFound'), 422
            );

            if(isset($data['rate']) && $data['rate'] < 1)
                return new GenericPayload(
                __('error.RateIsRequired'), 422);

            $rating->update($data);
            //return new GenericPayload(['message' =>  __('success.updatedSuccessfuly')]);
            return new GenericPayload([
                'message' =>  __('success.updatedSuccessfuly'),
                "rating" => new RatingResource($rating),
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
}
