<?php
namespace App\Tenant\Category\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class CategoryFilter extends QueryFilter
{

    public function isActive($is_active)
    {
        $this->builder->where('is_active', $is_active);
    }

    public function category($category_id)
    {
        $this->builder->where('parent_id', $category_id);
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
        })
        ->orWhere('created_at', 'like', '%'.$search.'%');
    }

    public function orderBy($orderBy)
    {
        $this->builder->when($orderBy != 'name', function($collection) use ($orderBy){
            return $collection->orderBy($orderBy, request()->orderType ?? 'DESC');
        })
        ->when($orderBy == 'name', function($collection) {
            return $collection->orderBy('category_translations.name', request()->orderType ?? 'DESC')
            ->where('category_translations.locale', app()->getLocale());
        });
    }


}
