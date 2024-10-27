<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Requests\CountryRequest;
use App\Tenant\Location\Domain\Services\UpdateCountryService;
use App\Tenant\Location\Responders\CountryResponder;
use App\Tenant\Location\Domain\Models\Country;

class UpdateCountryAction
{
    public function __construct(GenericResponder $responder, UpdateCountryService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CountryRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["country_id" => $id]))
        )->getResponseData();
    }
}
