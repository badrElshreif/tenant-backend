<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Repositories\BrandRepository;
use App\Tenant\Brand\Domain\Resources\BrandLiteResource;

class ListBrandsService extends Service
{
    protected $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function handle($data = [])
    {


        if (isset($data['is_paginated']) && $data['is_paginated'] == 1):
            $limit = $data['per_page'] ?? 10;
            $brands = $this->brandRepository->filter($data)->paginate($limit);
            return [
                'data' => BrandLiteResource::listCollection($brands),
                'status' => true,
                'message' => 'Brands List',
            ];
        else:
            $brands = $this->brandRepository->filter($data);
            return [
                'data' => BrandLiteResource::collection($brands),
                'status' => true,
                'message' => 'Brands List',
            ];
        endif;
    }

}
