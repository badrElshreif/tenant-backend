<?php

namespace App\Tenant\Order\Domain\Services\Payments;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Tenant\Order\Domain\Models\Order;
use Symfony\Component\HttpFoundation\Response;

class CreatePaymentService extends Service
{
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();

            $data['total'] = 0.000;
            $data['application_dues'] = 0.000;
            $refund_period = setting('refund_period');
            $refund_period = $refund_period ? : 14;

            $orders = Order::whereIn('id', $data['orders'])->get();

            foreach ($orders as $order) {
                if($order->store_id != $orders[0]->store_id)
                    return new GenericPayload( __('error.differentStore'), 422);

                $date = $order->created_at->addDays($refund_period);
                if($date > \Carbon\Carbon::now())
                    return new GenericPayload( __('error.wrongOrderId'), 422);

                if($order->payment_id != null)
                    return new GenericPayload( __('error.paidBefore'), 422);

                $data['total'] += $order->total;
                $data['application_dues'] += $order->application_dues;
            }

            $data['store_id'] = $orders[0]->store_id;
            $data['application_dues_percentage'] = $orders[0]->application_dues_percentage;
            $data['amount'] = $data['total'] - $data['application_dues'];

            $payment = Payment::create($data);

            foreach ($orders as $order) {
                $order->update([
                    'payment_id' => $payment->id
                ]);
            }

            // Commit Transaction
            DB::commit();

	        return new GenericPayload($payment, Response::HTTP_CREATED);

	    }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\PDOException $ex){
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (\Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }

    }

}
