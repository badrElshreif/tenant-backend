<?php

namespace App\Tenant\AppContent\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Faq;
use App\Tenant\AppContent\Domain\Filters\FaqFilter;

class ListFaqsService extends Service
{
    protected $faq, $filter;

    public function __construct(Faq $faq, FaqFilter $filter)
    {
        $this->faq = $faq;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $faq = $this->faq->filter($this->filter)->paginate(config('app.pagination_limit'));

        return new GenericPayload($faq);
    }
}
