<?php

namespace App\Tenant\Product\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Product\Domain\Requests\ProductRequest;
use App\Tenant\Product\Domain\Services\UpdateProductService;

class UpdateProductAction
{
    public function __construct(GenericResponder $responder, UpdateProductService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ProductRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["product_id" => $id]))
        )->respond();
    }
}
