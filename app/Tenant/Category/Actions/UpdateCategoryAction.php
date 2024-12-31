<?php

namespace App\Tenant\Category\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Category\Domain\Requests\CategoryRequest;
use App\Tenant\Category\Domain\Services\UpdateCategoryService;

class UpdateCategoryAction
{
    public function __construct(GenericResponder $responder, UpdateCategoryService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CategoryRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["category_id" => $id]))
        )->respond();
    }
}
