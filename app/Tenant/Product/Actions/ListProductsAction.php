<?php

namespace App\Tenant\Product\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Product\Domain\Requests\ProductRequest;
use App\Tenant\Product\Domain\Services\ListProductsService;
use App\Tenant\Product\Responders\ProductResponder;

class ListProductsAction
{
    private GenericResponder $responder;
    private ListProductsService $service;

    public function __construct(GenericResponder $responder, ListProductsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ProductRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->getResponseData();
    }
}
