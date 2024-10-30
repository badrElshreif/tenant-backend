<?php

namespace App\Property\Actions;
use App\Property\Domain\Requests\PropertyRequest;
use App\Property\Domain\Services\ListPropertiesService;
use App\Property\Responders\PropertyResponder;

class ListPropertiesAction 
{
    public function __construct(PropertyResponder $responder, ListPropertiesService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(PropertyRequest $request) 
    {
        return $this->responder->withResponse(
            $this->service->handle($request)
        )->respond();
    }
}