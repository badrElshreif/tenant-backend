<?php

namespace App\Tenant\Location\Actions;
use App\Tenant\Location\Domain\Services\ShowStateService;
use App\Tenant\Location\Responders\StateResponder;
use App\Tenant\Location\Domain\Models\State;

class ShowStateAction
{
    public function __construct(StateResponder $responder, ShowStateService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["state_id" => $id])
        )->respond();
    }
}
