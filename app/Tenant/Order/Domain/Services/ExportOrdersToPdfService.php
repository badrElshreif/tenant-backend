<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Filters\OrderFilter;
use App\Tenant\Order\Domain\Exports\OrdersExport;
use App\Tenant\Order\Domain\Resources\OrderMobileResource;
use PDF;
use Symfony\Component\HttpFoundation\Response;

class ExportOrdersToPdfService extends Service
{
    protected $order, $filter;

    public function __construct(Order $order, OrderFilter $filter)
    {
        $this->order = $order;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $store_id = auth()->user()->store_id;
        $orders = $this->order->when($store_id, function($collection) use ($store_id){
                return $collection->ofStore($store_id);
            })->filter($this->filter)->orderBy('id', 'desc')->get();
        $pdf = PDF::loadView('orders',compact('orders'));
        //return $pdf->download('orders.pdf');
        return new GenericPayload(
        	$pdf->download('orders.pdf'), Response::HTTP_RESET_CONTENT
        );
    }
}


