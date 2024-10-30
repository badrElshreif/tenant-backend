<?php

namespace App\Property\Actions;
use App\Property\Domain\Services\DeletePropertyService;
use App\Property\Responders\PropertyResponder;

class DeletePropertyAction 
{
    public function __construct(PropertyResponder $responder, DeletePropertyService $service) 
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