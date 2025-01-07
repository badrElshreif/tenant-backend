<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\CountryRepository;
use App\Tenant\Location\Domain\Resources\CountryLiteResource;

class ListCountriesService extends Service
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function handle($data = [])
    {
        $order = $data['orderBy'] ?? 'order';
        $order_type = $data['orderType'] ?? 'ASC';
        $limit = $data['per_page'] ?? 10;


        if (isset($data['is_paginated']) && $data['is_paginated'] == 1):
            $countries = $this->countryRepository->query($data, $order, $order_type)
                ->paginate($limit);
            return [
                'data' => CountryLiteResource::listCollection($countries),
                'status' => true,
                'message' => 'Countries List',
            ];
        endif;

        $countries = $this->countryRepository->query($data, $order, $order_type)->get();
        return [
            'data' => CountryLiteResource::collection($countries),
            'status' => true,
            'message' => 'Countries List',
        ];
    }
}
