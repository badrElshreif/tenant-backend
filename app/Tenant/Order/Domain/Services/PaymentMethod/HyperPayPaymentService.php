<?php

namespace App\Tenant\Order\Domain\Services\PaymentMethod;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HyperPayPaymentService extends Service
{

    public function handle($id=null)
    {
        $order=Order::withoutGlobalScopes()->where('is_paid',0)->whereNull('deleted_at')
            ->whereHas('paymentMethod',function ($q){
                $q->where('type','online');
            })
            ->whereHas('status',function ($query){
                $query->where(['key'=>'new','type'=>'order']);
            })
            ->findOrFail($id);
        $checkout=$order->transaction_id;
        $payment_method=$order->payment_method_brand;
        return new GenericPayload(view('hyperPay',compact('checkout','payment_method')), Response::HTTP_RESET_CONTENT);
    }
}
