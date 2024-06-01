<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Requests\UpdateOrderStatusFormRequest;
use App\Tenant\Order\Domain\Services\UpdateOrderStatusService;
use App\Tenant\Order\Responders\OrderResponder;

class UpdateOrderStatusAction
{
    public function __construct(OrderResponder $responder, UpdateOrderStatusService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(UpdateOrderStatusFormRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["order_id" => $id]))
        )->respond();
    }
}
