<?php

namespace App\Tenant\Order\Actions\Payments;
use App\Tenant\Order\Domain\Requests\PaymentRequest;
use App\Tenant\Order\Domain\Services\Payments\ListPaymentsService;
use App\Tenant\Order\Responders\PaymentResponder;

class ListPaymentsAction
{
    public function __construct(PaymentResponder $responder, ListPaymentsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(PaymentRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
