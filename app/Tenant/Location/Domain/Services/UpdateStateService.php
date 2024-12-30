<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Resources\StateResource;
use App\Tenant\Location\Domain\Models\State;
use Symfony\Component\HttpFoundation\Response;

class UpdateStateService extends Service
{
    public function handle($data = [])
    {
        try {
            $state = State::findOrFail($data['state_id']);
            $state->update($data);

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
