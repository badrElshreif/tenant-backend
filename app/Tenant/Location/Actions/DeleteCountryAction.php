<?php

namespace App\Tenant\Location\Actions;
use App\Tenant\Location\Domain\Services\DeleteCountryService;
use App\Tenant\Location\Responders\CountryResponder;
use App\Tenant\Location\Domain\Models\Country;

class DeleteCountryAction
{
    public function __construct(CountryResponder $responder, DeleteCountryService $services)
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
