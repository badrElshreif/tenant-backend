<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Illuminate\Support\Arr;
use App\Tenant\Location\Domain\Models\Country;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ShowCountryService extends Service
{
    public function handle($data = [])
    {
        $country = Country::findOrFail($data['country_id']);
        try {

            return new GenericPayload($country, Response::HTTP_CREATED);
        }catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
