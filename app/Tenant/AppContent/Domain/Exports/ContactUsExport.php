<?php

namespace App\Tenant\AppContent\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContactUsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public $ContactUs, $filter;
    public function __construct($ContactUs, $filter)
    {
        $this->ContactUs = $ContactUs;
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->ContactUs->filter($this->filter)->get();
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->email,
            $item->phone,
            $item->title,
            $item->body,
            $item->created_at->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Title',
            'Message',
            'Created At'
        ];
    }
}
