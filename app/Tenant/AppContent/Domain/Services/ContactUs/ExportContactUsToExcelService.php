<?php

namespace App\Tenant\AppContent\Domain\Services\ContactUs;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\ContactUs;
use App\Tenant\AppContent\Domain\Filters\ContactUsFilter;
use App\Tenant\AppContent\Domain\Exports\ContactUsExport;
use Excel;
use Symfony\Component\HttpFoundation\Response;

class ExportContactUsToExcelService extends Service
{
    protected $contact_us, $filter;

    public function __construct(ContactUs $contact_us, ContactUsFilter $filter)
    {
        $this->contact_us = $contact_us;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        return new GenericPayload(
        	Excel::download(new ContactUsExport($this->contact_us->whereNull('parent_id'), $this->filter), 'contact_us.xlsx')
            , Response::HTTP_RESET_CONTENT
        );
    }
}


