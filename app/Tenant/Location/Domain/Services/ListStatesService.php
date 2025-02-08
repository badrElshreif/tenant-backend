<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Filters\StateFilter;
use App\Tenant\Location\Domain\Repositories\StateRepository;
use App\Tenant\Location\Domain\Resources\StateLiteResource;
use Symfony\Component\HttpFoundation\Response;

class ListStatesService extends Service
{
    protected $stateRepository, $filter;

    public function __construct(StateRepository $stateRepository, StateFilter $filter)
    {
        $this->stateRepository = $stateRepository;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        if (isset($data['is_paginated']) && $data['is_paginated'] == 1):
            $limit = $data['per_page'] ?? 10;
            $states = $this->stateRepository->query($data)->paginate($limit);
            return [
                'data' => StateLiteResource::listCollection($states),
                'status' => true,
                'message' => 'States List',
            ];
        else:
            $states = $this->stateRepository->query($data)->get();
            return [
                'data' => StateLiteResource::collection($states),
                'status' => true,
                'message' => 'States List',
            ];
        endif;


    }
}
