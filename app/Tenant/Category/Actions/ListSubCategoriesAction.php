<?php

namespace App\Tenant\Category\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Category\Domain\Services\ListSubCategoriesService;
use App\Tenant\Category\Domain\Requests\CategoryRequest;

class ListSubCategoriesAction
{
    public function __construct(GenericResponder $responder, ListSubCategoriesService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CategoryRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
