<?php

namespace App\Tenant\Order\Actions;
use App\Tenant\Order\Domain\Services\ListOrderStatusesService;
use App\Tenant\Order\Responders\StatusResponder;

class ListOrderStatusesAction
{
    public function __construct(StatusResponder $responder, ListOrderStatusesService $service)
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
