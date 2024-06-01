<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Services\ShowOrderService;
use App\Tenant\Order\Responders\OrderResponder;

class ShowOrderAction
{
    public function __construct(OrderResponder $responder, ShowOrderService $service)
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
