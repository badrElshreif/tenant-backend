<?php

namespace App\Tenant\Order\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\User\Domain\Resources\UserAddressResource;
use DB;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
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

        return [
            $item->id,
            $item->user->name,
            $item->total,
            $item->status->name,
            $item->paymentMethod->name,
            optional(optional($item->userAddress)->city)->name,
            optional(optional($item->userAddress)->city)->state->name,
            optional(optional($item->userAddress)->city)->state->country->name,
            $item->created_at->format('Y-m-d'),

        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer',
            'Total',
            'Status',
            'Payment Method',
            'City',
            'State',
            'Country',
            'Date',
        ];
    }
}
