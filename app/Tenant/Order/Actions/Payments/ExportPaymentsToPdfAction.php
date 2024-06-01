<?php

namespace App\Tenant\Order\Actions\Payments;
use App\Tenant\Order\Domain\Services\Payments\ExportPaymentsToPdfService;
use App\Tenant\Order\Responders\PaymentResponder;

class ExportPaymentsToPdfAction
{
    public function __construct(PaymentResponder $responder, ExportPaymentsToPdfService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->service->handle()
        )->respond();
    }
}
