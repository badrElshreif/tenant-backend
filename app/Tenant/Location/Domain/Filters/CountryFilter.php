<?php
namespace App\Tenant\Location\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class CountryFilter extends QueryFilter
{

    public function status($status)
    {
        $this->builder->where('is_active', $status);
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('translations', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('id', $search);

    }


}
