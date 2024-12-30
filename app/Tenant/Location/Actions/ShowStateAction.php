<?php

namespace App\Tenant\Location\Actions;
use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Services\ShowStateService;

class ShowStateAction
{
    public function __construct(GenericResponder $responder, ShowStateService $services)
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
