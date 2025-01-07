<?php

namespace App\Tenant\Location\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class CountryFilter extends QueryFilter
{
    public function active($status)
    {
        if (!is_numeric($status)) {
            $status = $status == 'true' ? 1 : 0;
        }
        $this->builder->where('is_active', $status);
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('translations', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })
            ->orWhere('id', $search);

    }

}
