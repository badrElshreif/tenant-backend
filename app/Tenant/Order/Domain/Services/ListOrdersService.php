<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Models\OrderItem;
use App\Tenant\Order\Domain\Models\OrderGift;
use App\Tenant\Order\Domain\Models\Cart;
use App\Tenant\Order\Domain\Models\Coupon;
use App\Tenant\Order\Domain\Models\ShippingCompany;
use App\Product\Domain\Models\Product;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\User\Domain\Models\User;
use DB;
use App\Tenant\Order\Domain\Filters\OrderFilter;
use Symfony\Component\HttpFoundation\Response;

class ListOrdersService extends Service
{
    protected $order, $filter;

    public function __construct(Order $order, OrderFilter $filter)
    {
        $this->order = $order;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        try {
            $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
            $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
            $user_id = null;
            if(auth()->guard('api')->check())
                $user_id = auth('api')->id();

            $store_id = null;
            if(auth()->guard('store')->check() || auth()->guard('center')->check())
                $store_id = auth()->user()->store_id;
            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            $orders = $this->order->filter($this->filter)->with('orderItems', 'orderService')
            ->when($store_id, function($collection) use ($store_id){
                return $collection->ofStore($store_id);
            })
            ->when($user_id, function($collection) use ($user_id){
                return $collection->where('user_id', $user_id);
            })
            ->orderBy($order, $order_type)->paginate($limit);
            return new GenericPayload($orders, Response::HTTP_ACCEPTED);
        } catch (\Illuminate\Database\QueryException $ex) {
            return new GenericPayload(
               $ex->getMessage(), 422
            );
        } catch (\PDOException $ex){
            return new GenericPayload(
               $ex->getMessage(), 422
            );
        }
        catch (\Exception $ex) {
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        }
    }

}
