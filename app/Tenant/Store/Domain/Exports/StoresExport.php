<?php

namespace App\Tenant\Store\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoresExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public $store, $filter;
    public function __construct($store, $filter)
    {
        $this->store = $store;
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->store->filter($this->filter)->get();
    }

    public function map($item): array
    {
        $item_data = [
            $item->id,
            $item->name,
            $item->email,
            $item->phone,
            $item->created_at->format('Y-m-d'),
        ];
        if($item->status == 1){
            $active = $item->is_active == 1 ? 'Active' : 'Inactive';
            array_push($item_data, $active);
        }

        return $item_data;
    }

    public function headings(): array
    {
        $headings_data = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Created At'
        ];
        if(request()->status == 'accepted'){
            array_push($headings_data, 'Status');
        }
        return $headings_data;
    }
}
