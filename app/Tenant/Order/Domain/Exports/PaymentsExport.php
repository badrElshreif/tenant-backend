<?php

namespace App\Tenant\Order\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\User\Domain\Resources\UserAddressResource;
use DB;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public $payment, $filter;
    public function __construct($payment, $filter)
    {
        $this->payment = $payment;
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->payment->filter($this->filter)->get();
    }

    public function map($item): array
    {

        return [
            $item->id,
            $item->store->name,
            $item->total,
            $item->application_dues,
            $item->amount,
            $item->created_at->format('Y-m-d'),

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
        ];
    }
}
