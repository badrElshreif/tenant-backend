<?php

namespace App\Tenant\Category\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Category\Domain\Services\ShowCategoryService;

class ShowCategoryAction
{
    public function __construct(GenericResponder $responder, ShowCategoryService $services)
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
