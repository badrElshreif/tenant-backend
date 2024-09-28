<?php

namespace App\Tenant\AppContent\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FaqExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public $faq, $filter;
    public function __construct($faq, $filter)
    {
        $this->faq = $faq;
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->faq->filter($this->filter)->get();
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->question,
            $item->answer
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Question',
            'Answer'
        ];
    }
}
