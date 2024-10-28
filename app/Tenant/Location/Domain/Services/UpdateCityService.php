<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Resources\CityResource;
use App\Tenant\Location\Domain\Models\City;
use Symfony\Component\HttpFoundation\Response;

class UpdateCityService extends Service
{
    public function handle($data = [])
    {
        try {
            $city = City::findOrFail($data['city_id']);
            $city->update($data);

            return new GenericPayload($city, Response::HTTP_OK,
                ResponseType::SingleResource, CityResource::class);

        } catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

    }
}
