<?php

namespace App\Tenant\Admin\Actions\Admin;

use App\Tenant\Admin\Domain\Services\Admin\ExportAdminsToExcelService;
use App\Tenant\Admin\Responders\AdminResponder;

class ExportAdminsToExcelAction
{
    public function __construct(AdminResponder $responder, ExportAdminsToExcelService $service)
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
