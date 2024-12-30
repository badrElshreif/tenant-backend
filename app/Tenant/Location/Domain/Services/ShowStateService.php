<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\State;
use App\Tenant\Location\Domain\Resources\StateResource;

class ShowStateService extends Service
{
    public function handle($data = [])
    {
        $state = State::findOrFail($data['state_id']);

        return [
            'data' => new StateResource($state),
            'status' => true,
            'message' => 'State Show',
        ];
    }
}
