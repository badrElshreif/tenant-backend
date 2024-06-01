<?php

namespace App\Tenant\Order\Domain\Services\PaymentMethod;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Helpers\Payment\PaymentService;
use Symfony\Component\HttpFoundation\Response;

class ListOnlinePaymentMethodsService extends Service
{
    public $paymentService;

    public function __construct(PaymentService $paymentService)
    {
       $this->paymentService=$paymentService;
    }

    public function handle($data = [])
    {
        $methods = $this->paymentService->getMethods();
        return new GenericPayload($methods, Response::HTTP_OK);
    }
}
