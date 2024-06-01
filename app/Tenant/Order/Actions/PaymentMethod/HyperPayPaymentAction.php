<?php

namespace App\Tenant\Order\Actions\PaymentMethod;
use App\Tenant\Order\Domain\Services\PaymentMethod\HyperPayPaymentService;
use App\Tenant\Order\Responders\HyperPayPaymentResponder;

class HyperPayPaymentAction
{
    public function __construct(HyperPayPaymentResponder $responder, HyperPayPaymentService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle($id)
        )->respond();
    }
}
