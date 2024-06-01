<?php

namespace App\Tenant\Order\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\User\Domain\Resources\UserAddressResource;
use DB;

class FinancialDuesExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public $order, $filter;
    public function __construct($order, $filter)
    {
        $this->order = $order;
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->order->filter($this->filter)->get();
    }

    public function map($item): array
    {
        $refund_period = setting('refund_period');
        $refund_period = $refund_period ? : 10;
        $date = $item->created_at->addDays($refund_period);

        return [
            $item->id,
            $item->store->name,
            $item->total,
            $item->application_dues,
            number_format((float)($item->total - $item->application_dues), 2, '.', ''),
            \Carbon\Carbon::parse($date)->translatedFormat('d M Y'),
            \Carbon\Carbon::parse($date)->translatedFormat('H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Store',
            'Amount before deduction of commission',
            'commission',
            'Amount after deduction of commission',
            'Date',
            'Time'
        ];
    }
}
