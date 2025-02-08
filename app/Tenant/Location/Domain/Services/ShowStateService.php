<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\StateRepository;
use App\Tenant\Location\Domain\Resources\StateResource;

class ShowStateService extends Service
{

    protected $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function handle($data = [])
    {
        $state = $this->stateRepository->findOrFail($data['state_id']);

        return [
            'data' => new StateResource($state),
            'status' => true,
            'message' => 'State Show',
        ];
    }
}
