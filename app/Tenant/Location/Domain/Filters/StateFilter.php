<?php

namespace App\Tenant\Location\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class StateFilter extends QueryFilter
{
    public function status($status)
    {
        $this->builder->where('is_active', $status);
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('translations', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })
            ->orWhereHas('country', function ($q) use ($search) {
                $q->whereRelation('translations', 'name', 'like', '%' . $search . '%');
            })
            ->orWhere('country_id', $search)
            ->orWhere('id', $search);
    }

    public function country($country)
    {
        $this->builder->where('country_id', $country);
    }
}
