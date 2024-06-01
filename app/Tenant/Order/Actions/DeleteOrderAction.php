<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Services\DeleteOrderService;
use App\Tenant\Order\Responders\OrderResponder;

class DeleteOrderAction
{
    public function __construct(OrderResponder $responder, DeleteOrderService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["order_id" => $id])
        )->respond();
    }
}
