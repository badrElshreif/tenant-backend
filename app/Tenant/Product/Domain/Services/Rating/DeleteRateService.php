<?php

namespace App\Tenant\Product\Domain\Services\Rating;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Symfony\Component\HttpFoundation\Response;

class DeleteRateService extends Service
{
    public function handle($data = [])
    {
        try {
            $rating = auth()->user()->ratings->where('product_id', $data['product_id'])->first();
            if(!$rating)
                return new GenericPayload(
                __('error.notFound'), 422
            );
            $rating->delete();
            return new GenericPayload(['message' =>  __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);

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
