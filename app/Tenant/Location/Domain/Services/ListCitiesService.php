<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\City;
use App\Tenant\Location\Domain\Filters\CityFilter;
use Symfony\Component\HttpFoundation\Response;

class ListCitiesService extends Service
{
    protected $city, $filter;

    public function __construct(City $city, CityFilter $filter)
    {
        $this->city = $city;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $active = isset($data['active']) ? $data['active'] : 1;
        if(isset($data['active']) && $data['active'] == 'true')
            $active = 1;
        if(isset($data['active']) && $data['active'] == 'false')
            $active = 0;


        if( isset($data['is_paginated']) && $data['is_paginated'] == 1 ):
            $cities = $this->city->filter($this->filter)
            ->when(isset($data['active']), function($collection) use ($active){
                return $collection->active($active);
            })
            ->when($order == 'name', function($collection) use ($order_type){
                return $collection->join('city_translations', function ($join) {
                    $join->on('cities.id', '=', 'city_translations.city_id')
                        ->where('city_translations.locale', '=', app()->getLocale());
                })
                ->groupBy('cities.id')
                ->orderBy('city_translations.name', $order_type)
                ->select('cities.*', 'city_translations.id as city_translation_id');
            })
            ->when($order != 'name', function($collection) use ($order, $order_type){
                return $collection->orderBy($order, $order_type);
            })
            ->whereHas('state', function($q) {
                        $q->where('is_active', 1);
                    })
            ->paginate($limit);
            return new GenericPayload($cities, Response::HTTP_ACCEPTED);
        else:
            // if(auth('admin')->check())
            //     $cities = $this->city->filter($this->filter)->orderBy($order, $order_type)->get();
            // else
                $cities = $this->city->filter($this->filter)->whereIsActive(1)->orderBy($order, $order_type)->get();

            return new GenericPayload($cities, Response::HTTP_OK);
        endif;
    }
}
