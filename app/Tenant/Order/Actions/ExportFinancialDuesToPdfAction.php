<?php

namespace App\Tenant\Order\Actions;

use App\Tenant\Order\Domain\Services\ExportFinancialDuesToPdfService;
use App\Tenant\Order\Responders\OrderResponder;

class ExportFinancialDuesToPdfAction
{
    public function __construct(OrderResponder $responder, ExportFinancialDuesToPdfService $service)
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
