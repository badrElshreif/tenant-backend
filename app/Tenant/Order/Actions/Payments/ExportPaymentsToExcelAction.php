<?php

namespace App\Tenant\Order\Actions\Payments;
use App\Tenant\Order\Domain\Services\Payments\ExportPaymentsToExcelService;
use App\Tenant\Order\Responders\PaymentResponder;

class ExportPaymentsToExcelAction
{
    public function __construct(PaymentResponder $responder, ExportPaymentsToExcelService $service)
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
