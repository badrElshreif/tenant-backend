<?php

namespace App\Tenant\Order\Domain\Services\Payments;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Payment;
use App\Tenant\Order\Domain\Filters\PaymentFilter;
use App\Tenant\Order\Domain\Exports\PaymentsExport;
use Excel;
use Symfony\Component\HttpFoundation\Response;

class ExportPaymentsToExcelService extends Service
{
    protected $payment, $filter;

    public function __construct(Payment $payment, PaymentFilter $filter)
    {
        $this->payment = $payment;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $store_id = null;
        if(auth()->guard('store')->check() || auth()->guard('center')->check()){
            $store_id = auth()->user()->store_id;

              return new GenericPayload(
        	Excel::download(new PaymentsExport(
                $this->payment->where('store_id',$store_id)
                , $this->filter
            ), 'payments.xlsx'),
            Response::HTTP_RESET_CONTENT
        );
        }

        return new GenericPayload(
        	Excel::download(new PaymentsExport(
                $this->payment
                // ->when($store_id, function($collection) use ($store_id){
                //     return $collection->ofStore($store_id);
                // })
                , $this->filter
            ), 'payments.xlsx'),
            Response::HTTP_RESET_CONTENT
        );
    }
}


