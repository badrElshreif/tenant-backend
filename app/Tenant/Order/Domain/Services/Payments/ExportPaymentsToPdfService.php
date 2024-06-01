<?php

namespace App\Tenant\Order\Domain\Services\Payments;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Payment;
use App\Tenant\Order\Domain\Filters\PaymentFilter;
use PDF;
use MPDF;
use Symfony\Component\HttpFoundation\Response;

class ExportPaymentsToPdfService extends Service
{
    protected $payment, $filter;

    public function __construct(Payment $payment, PaymentFilter $filter)
    {
        $this->payment = $payment;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $payments = $this->payment->filter($this->filter)->orderBy('id', 'desc')->get();
        $pdf = MPDF::loadView('payments',compact('payments'));
        //return $pdf->download('payments.pdf');
        return new GenericPayload(
        	$pdf->download('payment.pdf'),
            Response::HTTP_RESET_CONTENT
        );
    }
}


