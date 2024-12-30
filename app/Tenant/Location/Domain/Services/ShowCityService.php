<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\City;
use App\Tenant\Location\Domain\Resources\CityResource;
use Symfony\Component\HttpFoundation\Response;

class ShowCityService extends Service
{
    public function handle($data = [])
    {

        $city = City::findOrFail($data['city_id']);

        return [
            'data' => new CityResource($city),
            'status' => true,
            'message' => 'City Show',
        ];
    }
}
