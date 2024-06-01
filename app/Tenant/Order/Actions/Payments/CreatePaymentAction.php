<?php

namespace App\Tenant\Order\Actions\Payments;
use App\Tenant\Order\Domain\Requests\PaymentRequest;
use App\Tenant\Order\Domain\Services\Payments\CreatePaymentService;
use App\Tenant\Order\Responders\PaymentResponder;

class CreatePaymentAction
{
    public function __construct(PaymentResponder $responder, CreatePaymentService $service)
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
