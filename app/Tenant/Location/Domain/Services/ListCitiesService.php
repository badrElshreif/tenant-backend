<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CityRepository;
use App\Tenant\Location\Domain\Resources\CityLiteResource;

class ListCitiesService extends Service
{
    protected $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function handle($data = [])
    {
        if (isset($data['is_paginated']) && $data['is_paginated'] == 1):
            $limit = $data['per_page'] ?? 10;
            $cities = $this->cityRepository->query($data)->paginate($limit);
            return [
                'data' => CityLiteResource::listCollection($cities),
                'status' => true,
                'message' => 'Cites List',
            ];
        else:
            $cities = $this->cityRepository->query($data)->get();

            return [
                'data' => CityLiteResource::collection($cities),
                'status' => true,
                'message' => 'States List',
            ];
        endif;
    }
}
