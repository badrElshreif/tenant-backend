<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CountryRepository;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Symfony\Component\HttpFoundation\Response;

class UpdateCountryService extends Service
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function handle($data = [])
    {
        try {
            $country = $this->countryRepository->updateOrFail($data['country_id'], $data);

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
