<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Requests\CityRequest;
use App\Tenant\Location\Domain\Services\UpdateCityService;
use App\Tenant\Location\Responders\CityResponder;

class UpdateCityAction
{
    public function __construct(GenericResponder $responder, UpdateCityService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CityRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["city_id" => $id]))
        )->getResponseData();
    }
}
