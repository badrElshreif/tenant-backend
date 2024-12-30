<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
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

            return [
                'status' => true,
                'data' => new CityResource($city),
                'message' => __('success.updatedSuccessfuly'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }

    }
}
