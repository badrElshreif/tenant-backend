<?php

namespace App\Property\Actions;
use App\Property\Domain\Services\TogglePropertyStatusService;
use App\Property\Responders\PropertyResponder;

class TogglePropertyStatusAction 
{
    public function __construct(PropertyResponder $responder, TogglePropertyStatusService $service) 
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