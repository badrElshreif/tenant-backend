<?php

namespace App\Tenant\Location\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Requests\CityRequest;
use App\Tenant\Location\Domain\Services\ListCitiesService;
use App\Tenant\Location\Responders\CityResponder;

class ListCitiesAction
{
    public function __construct(GenericResponder $responder, ListCitiesService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CityRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->getResponseData();
    }
}
