<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CountryRepository;
use Symfony\Component\HttpFoundation\Response;

class DeleteCountryService extends Service
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function handle($data = [])
    {
        try {
            $country = $this->countryRepository->findOrFail($data['country_id']);
            if ($country->states()->where('is_active', 1)->count() > 0)
                return [
                    'status' => false,
                    'message' => __('error.cannotDelete'),
                    'code' => Response::HTTP_NO_CONTENT,
                ];

            if ($country->addresses()->count() > 0)
                return [
                    'status' => false,
                    'message' => __('error.cannotDelete'),
                    'code' => Response::HTTP_NO_CONTENT,
                ];

            $country->forceDelete();
            return [
                'status' => true,
                'message' => __('success.deletedSuccessfuly'),
                'code' => Response::HTTP_NO_CONTENT,
            ];
        } catch (\Exception $ex) {
            return [
                'status' => false,
                'message' => $ex->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }
    }
}
