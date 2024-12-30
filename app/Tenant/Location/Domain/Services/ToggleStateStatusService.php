<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Resources\StateResource;
use App\Tenant\Location\Domain\Models\State;
use Symfony\Component\HttpFoundation\Response;

class ToggleStateStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $state = State::findOrFail($data['state_id']);

            if ($state->is_active) {
                if (count($state->cities()->where('is_active', 1)->get()) > 0)
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
