<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CityRepository;
use Symfony\Component\HttpFoundation\Response;

class DeleteCityService extends Service
{

    protected $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function handle($data = [])
    {
        try {
            $city = $this->cityRepository->findOrFail($data['city_id']);

            if ($city->addresses()->count() > 0)
                return [
                    'status' => false,
                    'message' => __('error.cannotDelete'),
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ];

            $city->delete();

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
