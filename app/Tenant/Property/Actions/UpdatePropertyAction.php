<?php

namespace App\Property\Actions;
use App\Property\Domain\Requests\PropertyRequest;
use App\Property\Domain\Services\UpdatePropertyService;
use App\Property\Responders\PropertyResponder;

class UpdatePropertyAction 
{
    public function __construct(PropertyResponder $responder, UpdatePropertyService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(PropertyRequest $request, $id) 
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["property_id" => $id]))
        )->respond();
    }
}