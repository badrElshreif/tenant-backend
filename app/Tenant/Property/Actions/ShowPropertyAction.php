<?php

namespace App\Property\Actions;
use App\Property\Domain\Services\ShowPropertyService;
use App\Property\Responders\PropertyResponder;

class ShowPropertyAction 
{
    public function __construct(PropertyResponder $responder, ShowPropertyService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id) 
    {
        return $this->responder->withResponse(
            $this->service->handle(["property_id" => $id])
        )->respond();
    }
}