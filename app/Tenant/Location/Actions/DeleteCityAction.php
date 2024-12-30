<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Services\DeleteCityService;

class DeleteCityAction
{
    public function __construct(GenericResponder $responder, DeleteCityService $services)
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
