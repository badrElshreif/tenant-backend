<?php

namespace App\Tenant\Location\Actions;
use App\Tenant\Location\Domain\Requests\CityRequest;
use App\Tenant\Location\Domain\Services\CreateCityService;
use App\Tenant\Location\Responders\CityResponder;

class CreateCityAction
{
    public function __construct(CityResponder $responder, CreateCityService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CityRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
