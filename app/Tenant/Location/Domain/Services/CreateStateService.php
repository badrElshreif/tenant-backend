<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Models\State;
use App\Tenant\Location\Domain\Resources\StateResource;
use Symfony\Component\HttpFoundation\Response;

class CreateStateService extends Service
{
    public function handle($data = [])
    {
        $state = State::create($data);
        return new GenericPayload($state, Response::HTTP_OK,
            ResponseType::SingleResource, StateResource::class,);

    }
}
