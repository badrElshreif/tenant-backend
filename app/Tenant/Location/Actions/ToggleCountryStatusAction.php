<?php

namespace App\Tenant\Location\Actions;
use App\Tenant\Location\Domain\Services\ToggleCountryStatusService;
use App\Tenant\Location\Responders\CountryResponder;

class ToggleCountryStatusAction
{
    public function __construct(CountryResponder $responder, ToggleCountryStatusService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["country_id" => $id])
        )->respond();
    }
}
