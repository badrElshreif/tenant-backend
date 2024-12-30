<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\State;
use App\Tenant\Location\Domain\Resources\StateResource;
use Symfony\Component\HttpFoundation\Response;

class CreateStateService extends Service
{
    public function handle($data = [])
    {
        try {
            $state = State::create($data);

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
