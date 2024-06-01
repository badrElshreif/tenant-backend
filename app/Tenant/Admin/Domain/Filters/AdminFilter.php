<?php

namespace App\Tenant\Admin\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class AdminFilter extends QueryFilter
{

    public function status($is_active)
    {
        if ($is_active == 'true')
            $is_active = 1;
        if ($is_active == 'false')
            $is_active = 0;
        $this->builder->where('is_active', $is_active);
    }

    public function email($email)
    {
        $this->builder->where('email', $email);
    }

    public function name($name)
    {
        $this->builder->where('name', 'like', '%' . $name . '%');
    }

    public function publicSearch($search)
    {
        $this->builder
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhereHas('roles', function ($query) use ($search) {
                $query->whereRelation('translations', 'name', 'like', '%' . $search . '%');
            });
    }

    public function orderBy($orderBy)
    {
        $this->builder->orderBy($orderBy, request()->orderType ?? 'DESC');
    }


}
