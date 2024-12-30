<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Resources\CountryResource;
use App\Tenant\Location\Domain\Models\Country;

class ShowCountryService extends Service
{

    public function handle($data = [])
    {
        $country = Country::findOrFail($data['country_id']);
        return [
            'data' => new CountryResource($country),
            'status' => true,
            'message' => 'Countries Show',
        ];
    }
}
