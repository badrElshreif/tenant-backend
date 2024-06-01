<?php

namespace App\Tenant\Order\Actions;

use App\Tenant\Order\Domain\Services\ExportOrdersToPdfService;
use App\Tenant\Order\Responders\OrderResponder;

class ExportOrdersToPdfAction
{
    public function __construct(OrderResponder $responder, ExportOrdersToPdfService $service)
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
