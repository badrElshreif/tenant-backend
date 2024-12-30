<?php

namespace App\Tenant\Location\Domain\Services;


use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\Country;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Symfony\Component\HttpFoundation\Response;

class CreateCountryService extends Service
{
    public function handle($data = [])
    {
        try {
            $country = Country::create($data);

            return [
                'data' => new CountryResource($country),
                'status' => true,
                'message' => 'Country Created',
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
