<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Requests\ServiceOrderRequest;
use App\Tenant\Order\Domain\Services\UpdateServiceOrderService;
use App\Tenant\Order\Responders\OrderResponder;

class UpdateServiceOrderAction
{
    public function __construct(OrderResponder $responder, UpdateServiceOrderService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ServiceOrderRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["order_id" => $id]))
        )->respond();
    }
}
