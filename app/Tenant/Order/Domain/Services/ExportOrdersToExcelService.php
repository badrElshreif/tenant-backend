<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Filters\OrderFilter;
use App\Tenant\Order\Domain\Exports\OrdersExport;
use Excel;
use Symfony\Component\HttpFoundation\Response;
use DB;


class ExportOrdersToExcelService extends Service
{
    protected $order, $filter;

    public function __construct(Order $order, OrderFilter $filter)
    {
        $this->order = $order;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $store_id = null;
        if(auth()->guard('store')->check() || auth()->guard('center')->check())
            $store_id = auth()->user()->store_id;
        return new GenericPayload(
        	Excel::download(new OrdersExport(
                $this->order->when($store_id, function($collection) use ($store_id){
                return $collection->ofStore($store_id);
            }),
                $this->filter
            ), 'orders.xlsx'),
            Response::HTTP_RESET_CONTENT
        );
    }
}


