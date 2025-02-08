<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\StateRepository;
use App\Tenant\Location\Domain\Resources\StateResource;
use App\Tenant\Location\Domain\Models\State;
use Symfony\Component\HttpFoundation\Response;

class UpdateStateService extends Service
{
    protected $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function handle($data = [])
    {
        try {
            $state = $this->stateRepository->update($data['state_id'], $data);

            return [
                'status' => true,
                'data' => new StateResource($state),
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
