<?php

namespace App\Tenant\Location\Actions;

use App\Tenant\Location\Domain\Services\ShowCountryService;
use App\Tenant\Location\Responders\CountryResponder;

class ShowCountryAction
{
    public function __construct(CountryResponder $responder, ShowCountryService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["country_id" => $id])
        )->getResponseData();
    }
}
