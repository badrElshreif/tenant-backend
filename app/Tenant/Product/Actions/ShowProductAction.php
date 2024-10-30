<?php

namespace App\Tenant\Product\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Product\Domain\Services\ShowProductService;
use App\Tenant\Product\Responders\ProductResponder;

class ShowProductAction
{
    public function __construct(GenericResponder $responder, ShowProductService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["product_id" => $id])
        )->getResponseData();
    }
}
