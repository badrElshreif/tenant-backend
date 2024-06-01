<?php

namespace App\Tenant\Product\Actions;
use App\Tenant\Product\Domain\Requests\ProductRequest;
use App\Tenant\Product\Domain\Services\ListProductsService;
use App\Tenant\Product\Responders\ProductResponder;

class ListProductsAction
{
    public function __construct(ProductResponder $responder, ListProductsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ProductRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
