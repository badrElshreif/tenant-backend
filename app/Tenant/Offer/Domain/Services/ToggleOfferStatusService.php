<?php

namespace App\Tenant\Offer\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Offer\Domain\Models\Offer;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ToggleOfferStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $offer = Offer::findOrFail($data['offer_id']);
            // if($offer->is_active == 1){
            //     if(count($offer->orders()->get()) > 0)
            //         return new GenericPayload(
            //              __('error.cannotDeactivate'), 422
            //         );
            // }
            $offer->update([
                'is_active' => !$offer->is_active
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }
        return new GenericPayload($offer, Response::HTTP_CREATED);

    }
}
