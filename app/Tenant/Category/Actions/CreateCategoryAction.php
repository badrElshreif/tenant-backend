<?php

namespace App\Tenant\Category\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Category\Domain\Requests\CategoryRequest;
use App\Tenant\Category\Domain\Services\CreateCategoryService;

class CreateCategoryAction
{
    public function __construct(GenericResponder $responder, CreateCategoryService $services)
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
