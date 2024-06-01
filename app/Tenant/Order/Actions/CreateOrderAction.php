<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Requests\OrderRequest;
use App\Tenant\Order\Domain\Services\CreateOrderService;
use App\Tenant\Order\Responders\OrderResponder;

class CreateOrderAction
{
    public function __construct(OrderResponder $responder, CreateOrderService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(OrderRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
