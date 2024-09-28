<?php
namespace App\Tenant\AppContent\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class PointFilter extends QueryFilter
{

    public function isActtive($status)
    {
        $this->builder->where('is_active', $status);
    }

    public function money($money)
    {
        $this->builder->where('money', $money);
    }

    public function pointsNumber($points_number)
    {
        $this->builder->where('points_number', $points_number);
    }

    public function category($category){
        $this->builder->where('category_id', $category)
        ->orWhereHas('category.translations', function($q) use ($category){
            $q->where('name', 'like', '%' . $category . '%');
        });
    }

    public function publicSearch($search){
        $this->builder->where('points_number', $search)
        ->orWhere('money', $search)
        ->orWhere('category_id', $search)
        ->orWhereHas('category.translations', function($q) use ($search){
            $q->where('name', 'like', '%' . $search . '%');
        });
    }
}
