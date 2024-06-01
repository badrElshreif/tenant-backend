<?php

namespace App\Tenant\Order\Actions\Payments;
use App\Tenant\Order\Domain\Requests\PaymentRequest;
use App\Tenant\Order\Domain\Services\Payments\ListPaymentsService;
use App\Tenant\Order\Domain\Services\Payments\TogglePaymentStatusService;
use App\Tenant\Order\Responders\PaymentMethodResponder;
use App\Tenant\Order\Responders\PaymentResponder;

class TogglePaymentStatusAction
{
    public function __construct(PaymentMethodResponder $responder, TogglePaymentStatusService $service)
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
