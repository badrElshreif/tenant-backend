<?php

namespace App\Tenant\Location\Actions;
use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Requests\CountryRequest;
use App\Tenant\Location\Domain\Services\ListCountriesService;
use App\Tenant\Location\Responders\CountryResponder;

class ListCountriesAction
{
    public function __construct(GenericResponder $responder, ListCountriesService $services)
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
