<?php

namespace App\Tenant\Location\Actions;
use App\Tenant\Location\Domain\Requests\CountryRequest;
use App\Tenant\Location\Domain\Services\CreateCountryService;
use App\Tenant\Location\Responders\CountryResponder;

class CreateCountryAction
{
    public function __construct(CountryResponder $responder, CreateCountryService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CountryRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
