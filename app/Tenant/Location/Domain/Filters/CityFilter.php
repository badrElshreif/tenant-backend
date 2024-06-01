<?php
namespace App\Tenant\Location\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class CityFilter extends QueryFilter
{

    public function status($status)
    {
        $this->builder->where('is_active', $status);
    }

    public function id($id)
    {
        $this->builder->where('id', $id);
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('translations', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('state', function($q) use ($search){
                $q->whereRelation('translations', 'name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('country', function($q) use ($search){
                $q->whereRelation('translations', 'name', 'like', '%' . $search . '%');
            })
            ->orWhere('state_id', $search)
            ->orWhere('country_id', $search)
            ->orWhere('id', $search);
    }

    public function state($state)
    {
        $this->builder->where('state_id', $state);
    }

    public function country($country)
    {
        $this->builder->where('country_id', $country);
    }


}
