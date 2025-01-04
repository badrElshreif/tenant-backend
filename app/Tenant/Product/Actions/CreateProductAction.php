<?php

namespace App\Tenant\Product\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Product\Domain\Requests\ProductRequest;
use App\Tenant\Product\Domain\Services\CreateProductService;

class CreateProductAction
{

    protected $service;
    protected $responder;

    public function __construct(GenericResponder $responder, CreateProductService $service)
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
