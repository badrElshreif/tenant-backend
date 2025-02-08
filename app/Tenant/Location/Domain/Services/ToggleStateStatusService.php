<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\StateRepository;
use App\Tenant\Location\Domain\Resources\StateResource;
use Symfony\Component\HttpFoundation\Response;

class ToggleStateStatusService extends Service
{
    protected $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function handle($data = [])
    {
        try {
            $state = $this->stateRepository->findOrFail($data['state_id']);

            if ($state->is_active) {
                if ($state->cities()->where('is_active', 1)->count() > 0)
                    return [
                        'status' => false,
                        'message' => __('error.cannotDeactivate'),
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ];

//                if (count($state->addresses()->get()) > 0)
//                    return new GenericPayload(
//                        __('error.cannotDeactivate'), 422
//                    );
            }

            $state->cities()->update([
                'is_active' => !$state->is_active
            ]);

            $state->update([
                'is_active' => !$state->is_active
            ]);

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
