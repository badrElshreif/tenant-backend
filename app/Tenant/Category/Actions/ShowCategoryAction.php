<?php

namespace App\Tenant\Category\Actions;
use App\Tenant\Category\Domain\Services\ShowCategoryService;
use App\Tenant\Category\Responders\CategoryResponder;

class ShowCategoryAction
{
    public function __construct(CategoryResponder $responder, ShowCategoryService $services)
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
