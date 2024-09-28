<?php

namespace App\Tenant\AppContent\Actions\API;

use App\Tenant\AppContent\Domain\Services\API\ExportFaqsToExcelService;
use App\Tenant\AppContent\Responders\API\ExportFaqsToExcelResponder;

class ExportFaqsToExcelAction
{
    public function __construct(ExportFaqsToExcelResponder $responder, ExportFaqsToExcelService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}
