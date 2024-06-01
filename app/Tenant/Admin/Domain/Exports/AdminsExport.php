<?php

namespace App\Tenant\Admin\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdminsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public $admin, $filter;
    public function __construct($admin, $filter)
    {
        $this->admin = $admin;
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->admin->filter($this->filter)->get();
    }

    public function map($item): array
    {
        $is_active = $item->is_active ? 'Active' : 'Inactive';
        return [
            $item->id,
            $item->name,
            $item->email,
            $item->phone,
            $is_active,
            \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y')
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Status',
            'Created Date'
        ];
    }
}
