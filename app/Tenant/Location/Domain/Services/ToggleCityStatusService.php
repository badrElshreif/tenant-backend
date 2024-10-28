<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Models\City;
use App\Tenant\Location\Domain\Resources\CityResource;
use Symfony\Component\HttpFoundation\Response;

class ToggleCityStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $city = City::findOrFail($data['city_id']);

            if ($city->is_active) {
                if (count($city->addresses()->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );
            }

            $city->update([
                'is_active' => !$city->is_active
            ]);
            return new GenericPayload($city, Response::HTTP_OK,
                ResponseType::SingleResource, CityResource::class);
        } catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

    }
}
