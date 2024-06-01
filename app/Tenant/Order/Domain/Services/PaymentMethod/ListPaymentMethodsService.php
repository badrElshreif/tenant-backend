<?php

namespace App\Tenant\Order\Domain\Services\PaymentMethod;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\PaymentMethod;
use Symfony\Component\HttpFoundation\Response;

class ListPaymentMethodsService extends Service
{
    public function handle($data = [])
    {
        // $methods = PaymentMethod::where('type', '!=', 'wallet')->where('is_active', 1)->get();
        $methods = PaymentMethod::when(!auth('admin')->check(),function ($q){
            $q->where('is_active', 1);
        })
        ->when(!auth('admin')->check() && !auth('store')->check() && !auth('center')->check(), function($collection){
                return $collection->where('type', '!=', 'wallet');
            })
        ->get();
        return new GenericPayload($methods, Response::HTTP_OK);
    }
}
