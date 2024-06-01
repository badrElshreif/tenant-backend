<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Requests\OrderRequest;
use App\Tenant\Order\Domain\Services\ListFinancialDuesService;
use App\Tenant\Order\Responders\OrderResponder;

class ListFinancialDuesAction
{
    public function __construct(OrderResponder $responder, ListFinancialDuesService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->service->handle(request()->all())
        )->respond();
    }
}
