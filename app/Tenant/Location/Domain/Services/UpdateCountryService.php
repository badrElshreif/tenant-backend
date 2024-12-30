<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Resources\CountryResource;
use App\Tenant\Location\Domain\Models\Country;
use Symfony\Component\HttpFoundation\Response;

class UpdateCountryService extends Service
{
    public function handle($data = [])
    {
        try {
            $country = Country::findOrFail($data['country_id']);

            $country->update($data);

            return [
                'status' => true,
                'data' => new CountryResource($country),
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
