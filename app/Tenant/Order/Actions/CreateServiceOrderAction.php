<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Requests\ServiceOrderRequest;
use App\Tenant\Order\Domain\Services\CreateServiceOrderService;
use App\Tenant\Order\Responders\OrderResponder;

class CreateServiceOrderAction
{
    public function __construct(OrderResponder $responder, CreateServiceOrderService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ServiceOrderRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
