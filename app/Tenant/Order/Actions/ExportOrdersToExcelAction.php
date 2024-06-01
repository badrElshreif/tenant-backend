<?php

namespace App\Tenant\Order\Actions;

use App\Tenant\Order\Domain\Services\ExportOrdersToExcelService;
use App\Tenant\Order\Responders\OrderResponder;

class ExportOrdersToExcelAction
{
    public function __construct(OrderResponder $responder, ExportOrdersToExcelService $service)
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
