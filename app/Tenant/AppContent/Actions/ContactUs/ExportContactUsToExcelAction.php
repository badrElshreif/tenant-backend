<?php

namespace App\Tenant\AppContent\Actions\ContactUs;

use App\Tenant\AppContent\Domain\Services\ContactUs\ExportContactUsToExcelService;
use App\Tenant\AppContent\Responders\ContactUsResponder;

class ExportContactUsToExcelAction
{
    public function __construct(ContactUsResponder $responder, ExportContactUsToExcelService $services)
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
