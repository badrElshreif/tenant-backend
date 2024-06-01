<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\Country;
use App\Tenant\Location\Domain\Models\City;
use Symfony\Component\HttpFoundation\Response;

class CreateCityService extends Service
{
    public function handle($data = [])
    {
        $city = City::create($data);
        return new GenericPayload($city, Response::HTTP_CREATED);

    }
}
