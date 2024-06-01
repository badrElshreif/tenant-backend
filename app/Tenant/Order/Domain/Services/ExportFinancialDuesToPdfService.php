<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Filters\OrderFilter;
use App\Tenant\Order\Domain\Exports\OrdersExport;
use App\Tenant\Order\Domain\Resources\OrderMobileResource;
use PDF;
use MPDF;
use Symfony\Component\HttpFoundation\Response;
use DB;

class ExportFinancialDuesToPdfService extends Service
{
    protected $order, $filter;

    public function __construct(Order $order, OrderFilter $filter)
    {
        $this->order = $order;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $refund_period = setting('refund_period');
        $refund_period = $refund_period ? : 10;

        $orders = $this->order->whereNull('payment_id')
                ->filter($this->filter)
                ->whereHas('status', function($q) {
                    $q->where('key', 'delivered');
                })
                ->whereDoesntHave('refund', function ( $query) {
                    $query->where('refund_type', '!=', 'order')->whereRelation('status', 'key', 'accepted');
                })
                ->where('created_at', '<', DB::raw('NOW() - INTERVAL ' . $refund_period . ' DAY'))->orderBy('id', 'desc')->get();
        $pdf = MPDF::loadView('financial_dues',compact('orders'));
      //  MPDF::loadView('bill', ['order' => $order], [], [])->save(storage_path("app/public/invoices/".$data['invoice_file']));

        //return $pdf->download('orders.pdf');
        return new GenericPayload(
        	$pdf->download('financial_dues.pdf'), Response::HTTP_RESET_CONTENT
        );
    }
}


