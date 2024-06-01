<?php

namespace App\Tenant\Order\Domain\Services\Payments;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Payment;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Order\Domain\Models\PaymentMethod;
use Symfony\Component\HttpFoundation\Response;

class TogglePaymentStatusService extends Service
{
    public function handle($id =null)
    {
        try {
            $payment = PaymentMethod::findOrFail($id);
            $payment->is_active=!$payment->is_active;
            $payment->save();
            return new GenericPayload($payment, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }
}
