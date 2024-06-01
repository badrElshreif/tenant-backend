<?php

namespace App\Tenant\Product\Actions;
use App\Tenant\Product\Domain\Requests\ProductRequest;
use App\Tenant\Product\Domain\Services\ListMostSellingProductsService;
use App\Tenant\Product\Responders\ProductResponder;

class ListMostSellingProductsAction
{
    public function __construct(ProductResponder $responder, ListMostSellingProductsService $service)
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
