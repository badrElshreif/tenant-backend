<?php

namespace App\Tenant\Order\Actions\PaymentMethod;
use App\Tenant\Order\Domain\Services\PaymentMethod\ListOnlinePaymentMethodsService;
use App\Tenant\Order\Responders\OnlinePaymentMethodResponder;

class ListOnlinePaymentMethodsAction
{
    public function __construct(OnlinePaymentMethodResponder $responder, ListOnlinePaymentMethodsService $service)
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
