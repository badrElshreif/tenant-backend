<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CityRepository;
use App\Tenant\Location\Domain\Resources\CityResource;
use Symfony\Component\HttpFoundation\Response;

class UpdateCityService extends Service
{
    protected $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function handle($data = [])
    {
        try {
            
            $city = $this->cityRepository->update($data['city_id'], $data);
            return [
                'status' => true,
                'data' => new CityResource($city),
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
