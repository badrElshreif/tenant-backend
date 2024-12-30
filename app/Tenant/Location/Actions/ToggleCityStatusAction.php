<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Services\ToggleCityStatusService;

class ToggleCityStatusAction
{
    public function __construct(GenericResponder $responder, ToggleCityStatusService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["city_id" => $id])
        )->respond();
    }
}
