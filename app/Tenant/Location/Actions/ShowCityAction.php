<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Services\ShowCityService;
use App\Tenant\Location\Responders\CityResponder;

class ShowCityAction
{
    public function __construct(GenericResponder $responder, ShowCityService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["city_id" => $id])
        )->getResponseData();
    }
}
