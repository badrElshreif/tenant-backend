<?php
namespace App\Tenant\Brand\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class BrandFilter extends QueryFilter
{

    public function isActive($is_active)
    {
        $this->builder->where('is_active', $is_active);
    }

    public function name($name)
    {
        $this->builder->whereHas('translations', function($q) use ($name) {
                $q->where('name', 'like', '%' . $name . '%');
            });
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('translations', function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }

    public function orderBy($orderBy)
    {
        $this->builder->when($orderBy != 'name', function($collection) use ($orderBy){
            return $collection->orderBy($orderBy, request()->orderType ?? 'DESC');
        })
        ->when($orderBy == 'name', function($collection) {
            return $collection->orderBy('brand_translations.name', request()->orderType ?? 'DESC')
            ->where('brand_translations.locale', app()->getLocale());
        });
    }


}
