<?php

namespace App\Tenant\Category\Actions;
use App\Tenant\Category\Domain\Services\DeleteCategoryService;
use App\Tenant\Category\Responders\CategoryResponder;

class DeleteCategoryAction
{
    public function __construct(CategoryResponder $responder, DeleteCategoryService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["category_id" => $id])
        )->respond();
    }
}
