<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ShowOrderService extends Service
{
    public function handle($data = [])
    {
        try {
            $order = Order::findOrFail($data['order_id']);
            // if($order->user_id != auth()->id() && (!auth()->guard('admin')->check() || !auth()->guard('store')->check() || !auth()->guard('center')->check()))
            //     return new GenericPayload(__('error.orders.userNotOrderOwner'), 422);
            return new GenericPayload($order, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }
}
