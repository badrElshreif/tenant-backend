<?php

namespace App\Tenant\Order\Actions\Transaction;
use App\Tenant\Order\Domain\Requests\TransactionFormRequest;
use App\Tenant\Order\Domain\Services\Transaction\ListUserTransactionsService;
use App\Tenant\Order\Responders\TransactionResponder;
class ListUserTransactionsAction
{
    public function __construct(TransactionResponder $responder, ListUserTransactionsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(TransactionFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle()
        )->respond();
    }
}
