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
use App\Tenant\Order\Domain\Resources\FinancialDuesResource;
use App\Infrastructure\Helpers\Traits\ApiPaginator;

class ListFinancialDuesService extends Service
{
    use ApiPaginator;
    protected $order, $filter;

    public function __construct(Order $order, OrderFilter $filter)
    {
        $this->order = $order;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        try {

            $refund_period = setting('refund_period');
            $refund_period = $refund_period ? : 10;

            // $store_id = null;
            // if(auth()->guard('store')->check() || auth()->guard('center')->check())
            //     $store_id = auth()->user()->store_id;

            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');

            $collection = $this->order->whereNull('payment_id')
            ->filter($this->filter)
            ->whereHas('status', function($q) {
                $q->where('key', 'delivered');
            })
            ->whereDoesntHave('refund', function ( $query) {
                $query->where('refund_type', '!=', 'order')->whereRelation('status', 'key', 'accepted');
            })
            //MySQL interval values are used mainly for date and time calculations, for details visit :https://www.mysqltutorial.org/mysql-interval/
            ->where('orders.created_at', '<', DB::raw('NOW() - INTERVAL ' . $refund_period . ' DAY'))
            ->paginate($limit);

           // $orders = FinancialDuesResource::collection($collection);
            $orders = $this->getPaginatedResponse(
                    $collection,
                    FinancialDuesResource::collection($collection)
                );
            return new GenericPayload($orders, Response::HTTP_RESET_CONTENT);
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
