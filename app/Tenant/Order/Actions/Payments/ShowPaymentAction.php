<?php

namespace App\Tenant\Order\Actions\Payments;
use App\Tenant\Order\Domain\Services\Payments\ShowPaymentService;
use App\Tenant\Order\Responders\PaymentResponder;

class ShowPaymentAction
{
    public function __construct(PaymentResponder $responder, ShowPaymentService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["payment_id" => $id])
        )->respond();
    }
}
