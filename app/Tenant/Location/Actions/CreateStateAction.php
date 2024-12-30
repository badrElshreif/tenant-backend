<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Requests\StateRequest;
use App\Tenant\Location\Domain\Services\CreateStateService;

class CreateStateAction
{
    public function __construct(GenericResponder $responder, CreateStateService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(StateRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
