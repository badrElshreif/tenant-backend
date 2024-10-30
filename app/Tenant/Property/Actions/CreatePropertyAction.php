<?php

namespace App\Property\Actions;
use App\Property\Domain\Requests\PropertyRequest;
use App\Property\Domain\Services\CreatePropertyService;
use App\Property\Responders\PropertyResponder;

class CreatePropertyAction 
{
    public function __construct(PropertyResponder $responder, CreatePropertyService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(PropertyRequest $request) 
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}