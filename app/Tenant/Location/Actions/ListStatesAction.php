<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Requests\StateRequest;
use App\Tenant\Location\Domain\Services\ListStatesService;

class ListStatesAction
{
    public function __construct(GenericResponder $responder, ListStatesService $services)
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
