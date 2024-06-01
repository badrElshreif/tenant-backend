<?php
namespace App\Tenant\Admin\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class RoleFilter extends QueryFilter
{

    public function status($is_active)
    {
        if($is_active == 'true')
            $is_active = 1;
        if($is_active == 'false')
            $is_active = 0;
        $this->builder->where('is_active', $is_active);
    }

    public function name($name)
    {
        $this->builder->whereHas('translations', function($q) use ($name) {
                $q->where('display_name', 'like', '%' . $name . '%');
            });
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('translations', function($q) use ($search) {
            $q->where('display_name', 'like', '%' . $search . '%');
        });
    }


    public function orderBy($orderBy)
    {
        $this->builder->when($orderBy != 'display_name', function($collection) use ($orderBy){
            return $collection->orderBy($orderBy, request()->orderType ?? 'DESC');
        })
        ->when($orderBy == 'display_name', function($collection) {
            return $collection->join('role_translations as trans', 'roles.id', '=', 'trans.role_id')
            ->orderBy('trans.display_name', request()->orderType ?? 'DESC')
            ->where('trans.locale', app()->getLocale());
        });
    }


}
