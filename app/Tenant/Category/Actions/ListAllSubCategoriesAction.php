<?php

namespace App\Tenant\Category\Actions;

use App\Tenant\Category\Domain\Services\ListSubCategoriesService;
use App\Tenant\Category\Responders\CategoryResponder;
use Illuminate\Http\Request;
use App\Tenant\Category\Domain\Requests\CategoryRequest;
class ListAllSubCategoriesAction
{
    public function __construct(CategoryResponder $responder, ListSubCategoriesService $services)
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
