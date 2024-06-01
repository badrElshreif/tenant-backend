<?php
namespace App\Tenant\Product\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class RatingFilter extends QueryFilter
{

    public function orderBy($orderBy)
    {
        $this->builder->orderBy($orderBy, request()->orderType ?? 'DESC');
    }


}
