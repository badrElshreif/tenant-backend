<?php

namespace App\Tenant\Category\Actions;
use App\Tenant\Category\Domain\Services\ToggleCategoryStatusService;
use App\Tenant\Category\Responders\CategoryResponder;

class ToggleCategoryStatusAction
{
    public function __construct(CategoryResponder $responder, ToggleCategoryStatusService $services)
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
