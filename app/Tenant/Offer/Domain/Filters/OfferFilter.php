<?php
namespace App\Tenant\Offer\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class OfferFilter extends QueryFilter
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
        //$this->builder->where('type', $search);
        $this->builder->whereHas('translations', function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })
        ->orWhere('type', 'like', '%' . $search. '%');
        // ->orWhereHas('products.translations', function($q) use ($search){
        //     $q->where('name', 'like', '%' . $search . '%');
        // });
    }

    public function productName($search)
    {
        $this->builder->whereHas('products.translations', function($q) use ($search){
            $q->where('name', 'like', '%' . $search . '%');
            // $q->whereTranslationLike('name','like', '%'.$search.'%');
        });
    }
    //offer type : 'value','percentage','free_product','free_charge'
    public function type($type)
    {
        $this->builder->where('type', $type);
    }

    public function date($date)
    {

        //$this->builder->where('created_at', 'like', '%'. $date.'%');
        $date = Carbon::parse($date)->toDateTimeString();
        //dd($date);4>=8, 7<=8
        $this->builder->where([
            ['start_date', '<=', $date],
            ['end_date', '>=', $date]
        ]);
        //->orWhereDate('end_date', '<=', $date);
    }

    public function orderBy($orderBy)
    {
        $this->builder->when($orderBy != 'name', function($collection) use ($orderBy){
            return $collection->orderBy($orderBy, request()->orderType ?? 'DESC');
        })
        ->when($orderBy == 'name', function($collection) {
            return $collection->orderBy('offer_translations.name', request()->orderType ?? 'DESC')
            ->where('offer_translations.locale', app()->getLocale());
        });
    }
}
