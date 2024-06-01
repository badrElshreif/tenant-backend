<?php

namespace App\Tenant\Store\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;
use Illuminate\Support\Facades\DB;

class StoreFilter extends QueryFilter
{

    public function isActive($is_active)
    {
        if ($is_active == 'true')
            $is_active = 1;
        if ($is_active == 'false')
            $is_active = 0;
        $this->builder->where('is_active', $is_active);
    }

    public function name($name)
    {
        $this->builder->whereRelation('translations', 'name', 'like', '%' . $name . '%');
    }

    public function publicSearch($search)
    {
        if (str_starts_with($search, '9660')) {
            $search = substr($search, 4, 20);
            $search = '966' . $search;
        }

        $this->builder->whereRelation('translations', 'name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%');
    }

    public function status($status)
    {
        if ($status == 'new') {
            $statuses = [0];
        } else if ($status == 'current') {
            $statuses = [1];
        } else if ($status == 'accepted') {
            $statuses = [1];
        } else if ($status == 'rejected') {
            $statuses = [2];
        } else {
            $statuses = array($status);
        }
        $this->builder->whereIn('status', $statuses);
    }

    public function email($email)
    {
        $this->builder->where('email', $email);
    }

    public function phone($phone)
    {
        if (str_starts_with($phone, '9660')) {
            $phone = substr($phone, 4, 20);
            $phone = '966' . $phone;
        }

        $this->builder->where('phone', $phone);
    }

    public function city($city_id)
    {
        $this->builder->where('city_id', $city_id);
    }

    public function state($state_id)
    {
        $this->builder->whereHas('city', function ($q) use ($state_id) {
            $q->where('state_id', $state_id);
        });
    }

    public function country($country_id)
    {
        $this->builder->whereHas('city', function ($q) use ($country_id) {
            $q->where('country_id', $country_id);
        });
    }

    public function type($type)
    {
        $this->builder->where('type', $type);
    }

//    public function latitude($latitude)
//    {
//        $longitude = request('longitude');
//        $this->builder->select(DB::raw('*, ( 6371 * acos( cos( radians(' .
//            $latitude . ') ) * cos( radians( `latitude` ) ) * cos(radians( `longitude` ) - radians(' .
//            $longitude . ') ) + sin( radians(' . $latitude .
//            ') ) * sin( radians( `latitude` ) ) ) ) as distance'))
////            ->having('distance', '<=', 50)
//            ->orderBy('distance');
//    }

    public function orderBy($orderBy)
    {
        $this->builder->when($orderBy != 'name', function ($collection) use ($orderBy) {
            return $collection->orderBy($orderBy, request()->orderType ?? 'DESC');
        })
            ->when($orderBy == 'name', function ($collection) {
                return $collection->join('store_translations as trans', 'stores.id', '=', 'trans.store_id')
                    ->orderBy('trans.name', request()->orderType ?? 'DESC')
                    ->where('trans.locale', app()->getLocale());
            });
    }
}
