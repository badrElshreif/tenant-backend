<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Illuminate\Support\Arr;
use App\Tenant\Location\Domain\Models\Country;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class DeleteCountryService extends Service
{
    public function handle($data = [])
    {
        try {
            $country = Country::findOrFail($data['country_id']);
            if(count($country->states()->where('is_active',1)->get()) > 0)
                return new GenericPayload(
                    __('error.cannotDelete'), 422
                );

            if(count($country->addresses()->get()) > 0)
                return new GenericPayload(
                    __('error.cannotDelete'), 422
                );

        	$country->forceDelete();
            return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
