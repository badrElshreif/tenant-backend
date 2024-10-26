<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Resources\CountryResource;
use App\Tenant\Location\Domain\Models\Country;
use Symfony\Component\HttpFoundation\Response;

class ShowCountryService extends Service
{
    public function handle($data = [])
    {
        $country = Country::findOrFail($data['country_id']);
        try {

            return new GenericPayload($country,
                Response::HTTP_OK,
                ResponseType::SingleResource,
                CountryResource::class,
            );

        } catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
