<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
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
            return new GenericPayload($state, Response::HTTP_OK,
                ResponseType::SingleResource, StateResource::class,);

        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
