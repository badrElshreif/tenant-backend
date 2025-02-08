<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\StateRepository;
use App\Tenant\Location\Domain\Resources\StateResource;
use Symfony\Component\HttpFoundation\Response;

class CreateStateService extends Service
{
    protected $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function handle($data = [])
    {
        try {
            $state = $this->stateRepository->create($data);

            return [
                'data' => new StateResource($state),
                'status' => true,
                'message' => 'State Created Successfully',
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
