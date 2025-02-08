<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CityRepository;
use App\Tenant\Location\Domain\Resources\CityResource;
use Symfony\Component\HttpFoundation\Response;

class ToggleCityStatusService extends Service
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

//            if ($city->is_active) {
//                if (count($city->addresses()->get()) > 0)
//                    return [
//                        'status' => false,
//                        'message' => __('error.cannotDeactivate'),
//                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
//                    ];
//            }

            $city->update([
                'is_active' => !$city->is_active
            ]);

            return [
                'status' => true,
                'data' => new CityResource($city),
                'message' => __('success.updatedSuccessfuly'),
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
