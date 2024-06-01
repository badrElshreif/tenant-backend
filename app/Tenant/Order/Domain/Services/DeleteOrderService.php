<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class DeleteOrderService extends Service
{
    public function handle($data = [])
    {
        try {
            $order = Order::findOrFail($data['order_id']);
            if($order->status->key == 'delivered')
                return new GenericPayload(
                    __('error.cannotDeleteOrder'), 422
                );
            $order->delete();
            return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }
}
