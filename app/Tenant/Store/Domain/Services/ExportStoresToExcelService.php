<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Tenant\Store\Domain\Filters\StoreFilter;
use App\Tenant\Store\Domain\Exports\StoresExport;
use Excel;
use Symfony\Component\HttpFoundation\Response;


class ExportStoresToExcelService extends Service
{
    protected $store, $filter;

    public function __construct(Store $store, StoreFilter $filter)
    {
        $this->store = $store;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        return new GenericPayload(
        	Excel::download(new StoresExport($this->store, $this->filter), 'stores.xlsx'),
            Response::HTTP_RESET_CONTENT
        );
    }
}


