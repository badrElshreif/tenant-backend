<?php

namespace App\Tenant\Offer\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Offer\Domain\Models\Offer;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class DeleteOfferService extends Service
{
    public function handle($data = [])
    {
        try {
            $offer = Offer::findOrFail($data['offer_id']);
             if(count($offer->orders()->get()) > 0)
                return new GenericPayload(
                    __('error.cannotDelete'), 422
                );
          //   if(count($offer->products()->get()) > 0)
        		// return new GenericPayload(
          //           __('error.cannotDelete'), 422
          //       );
            $offer->products()->detach();
            $offer->delete();

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
        return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);

    }
}
