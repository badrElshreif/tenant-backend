<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Requests\UpdateOrdersStatusFormRequest;
use App\Tenant\Order\Domain\Services\UpdateOrdersStatusService;
use App\Tenant\Order\Responders\OrderResponder;

class UpdateOrdersStatusAction
{
    public function __construct(OrderResponder $responder, UpdateOrdersStatusService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(UpdateOrdersStatusFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
