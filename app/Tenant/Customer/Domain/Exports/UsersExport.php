<?php

namespace App\User\Domain\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public $user, $filter;
    public function __construct($user, $filter)
    {
        $this->user = $user;
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->user->filter($this->filter)->get();
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->email,
            $item->country_code,
            $item->phone
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Country Code',
            'Phone'
        ];
    }
}