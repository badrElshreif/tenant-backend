<?php

namespace App\Tenant\Order\Actions;

use App\Tenant\Order\Domain\Services\ExportFinancialDuesToExcelService;
use App\Tenant\Order\Responders\OrderResponder;

class ExportFinancialDuesToExcelAction
{
    public function __construct(OrderResponder $responder, ExportFinancialDuesToExcelService $service)
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
