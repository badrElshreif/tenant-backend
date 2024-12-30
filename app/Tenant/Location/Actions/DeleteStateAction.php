<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Services\DeleteStateService;

class DeleteStateAction
{
    public function __construct(GenericResponder $responder, DeleteStateService $services)
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
