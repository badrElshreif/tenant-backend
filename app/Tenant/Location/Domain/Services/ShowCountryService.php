<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CountryRepository;
use App\Tenant\Location\Domain\Resources\CountryResource;

class ShowCountryService extends Service
{

    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function handle($data = [])
    {
        $country = $this->countryRepository->findOrFail($data['country_id']);
        return [
            'data' => new CountryResource($country),
            'status' => true,
            'message' => 'Country Show',
        ];
    }
}
