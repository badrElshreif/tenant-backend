<?php

namespace App\Tenant\AppContent\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Faq;
use App\Tenant\AppContent\Domain\Filters\FaqFilter;
use App\Tenant\AppContent\Domain\Exports\FaqExport;
use Excel;

class ExportFaqsToExcelService extends Service
{
    protected $faq, $filter;

    public function __construct(Faq $faq, FaqFilter $filter)
    {
        $this->faq = $faq;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        return new GenericPayload(
        	Excel::download(new FaqExport($this->faq, $this->filter), 'faqs.xlsx')
        );
    }
}


