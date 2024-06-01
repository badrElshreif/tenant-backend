<?php

namespace App\Tenant\Category\Actions;
use App\Tenant\Category\Domain\Requests\CategoryRequest;
use App\Tenant\Category\Domain\Services\ListAllCategoriesService;
use App\Tenant\Category\Responders\CategoryResponder;

class ListAllCategoriesAction
{
    public function __construct(CategoryResponder $responder, ListAllCategoriesService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CategoryRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request)
        )->respond();
    }
}
