<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Requests\UpdateOrderStatusFormRequest;
use App\Tenant\Order\Domain\Services\UpdateUserOrderStatusService;
use App\Tenant\Order\Responders\OrderResponder;

class UpdateUserOrderStatusAction
{
    public function __construct(OrderResponder $responder, UpdateUserOrderStatusService $service)
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
