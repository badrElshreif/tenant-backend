<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Models\Country;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class CreateCountryService extends Service
{
    public function handle($data = [])
    {
        $country = Country::create($data);

        return new GenericPayload($country, Response::HTTP_OK,
            ResponseType::SingleResource,
            CountryResource::class
        );

    }
}
