<?php

namespace App\Property\Actions;

use App\Property\Domain\Services\ListPropertyTypesService;
use App\Property\Responders\PropertyTypeResponder;

class ListPropertyTypesAction 
{
    public function __construct(PropertyTypeResponder $responder, ListPropertyTypesService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke() 
    {
        return $this->responder->withResponse(
            $this->service->handle()
        )->respond();
    }
}