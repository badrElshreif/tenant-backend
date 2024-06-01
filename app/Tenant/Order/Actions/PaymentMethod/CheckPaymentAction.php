<?php

namespace App\Tenant\Order\Actions\PaymentMethod;
use App\Tenant\Order\Domain\Services\PaymentMethod\CheckPaymentService;
use App\Tenant\Order\Responders\CheckPaymentResponder;

class CheckPaymentAction
{
    public function __construct(CheckPaymentResponder $responder, CheckPaymentService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->service->handle(request()->all())
        )->respond();
    }
}
