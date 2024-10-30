<?php

namespace App\Property\Actions;
use App\Property\Domain\Services\ListCategoryPropertiesService;
use App\Property\Responders\PropertyResponder;

class ListCategoryPropertiesAction 
{
    public function __construct(PropertyResponder $responder, ListCategoryPropertiesService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id) 
    {
        return $this->responder->withResponse(
            $this->service->handle(['category_id' => $id])
        )->respond();
    }
}