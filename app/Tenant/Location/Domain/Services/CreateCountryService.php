<?php

namespace App\Tenant\Location\Domain\Services;


use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CountryRepository;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Symfony\Component\HttpFoundation\Response;

class CreateCountryService extends Service
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function handle($data = [])
    {
        try {
            $country = $this->countryRepository->create($data);

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
