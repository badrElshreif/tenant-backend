<?php

namespace App\Tenant\Product\Domain\Services\Rating;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Resources\RatingResource;
use Symfony\Component\HttpFoundation\Response;

class AddRateService extends Service
{
    public function handle($data = [])
    {
        try {
            $can_rate_setting = setting('can_rate');
            if(!$can_rate_setting)
                return new GenericPayload(
                __('error.cannotRate'), 422);

            if(!auth()->check())
                return new GenericPayload(
                __('error.cannotRate'), 422);

            if($data['rate'] < 1)
                return new GenericPayload(
                __('error.RateIsRequired'), 422);

            $user = auth()->user();
            $data['is_active'] = 0;
            $rating = $user->ratings->where('product_id', $data['product_id'])->first();
            if($rating){
                $rating->update($data);
                // return new GenericPayload(
                //     __('error.productRatedBefore'), 422
                // );
            }else{
                $rating = $user->ratings()->create($data);
            }


            return new GenericPayload([
                'message' =>  __('success.productRated'),
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
