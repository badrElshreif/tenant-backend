<?php

namespace App\Infrastructure\Collections;

use App\Infrastructure\Traits\ApiPaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomCollection extends Collection
{
    use ApiPaginator;

    public function paginate($perPage = 15, $currentPage = null, $options = [])
    {
        $currentPage = $currentPage ?: LengthAwarePaginator::resolveCurrentPage();

        $items = $this->forPage($currentPage, $perPage);


        return new LengthAwarePaginator(
            $items,
            $this->count(),
            $perPage,
            $currentPage,
            array_merge(['path' => LengthAwarePaginator::resolveCurrentPath()], $options)
        );
    }
}
