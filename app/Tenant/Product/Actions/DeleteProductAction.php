<?php

namespace App\Tenant\Product\Actions;
use App\Tenant\Product\Domain\Services\DeleteProductService;
use App\Tenant\Product\Responders\ProductResponder;

class DeleteProductAction
{
    public function __construct(ProductResponder $responder, DeleteProductService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["product_id" => $id])
        )->respond();
    }
}
