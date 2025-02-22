
<?php

namespace App\Tenant\Location\Domain\Repositories;


use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Location\Domain\Models\City;

class CityRepository extends Repository
{

    public function __construct(City $city)
    {
        parent::__construct($city);
    }

    public function query($request)
    {
        $order = $request['order_by'] ?? 'id';
        $order_type = $request['order_type'] ?? 'ASC';

        $this->model = $this->model
            ->when(isset($request['active']), function ($collection) use ($request) {
                $collection->where('is_active', $request['active']);
            })->when($order == 'name', function ($collection) use ($order_type) {
                return $collection->join('city_translations', function ($join) {
                    $join->on('cities.id', '=', 'city_translations.city_id')
                        ->where('city_translations.locale', '=', app()->getLocale());
                })
                    ->groupBy('cities.id')
                    ->orderBy('city_translations.name', $order_type)
                    ->select('cities.*', 'city_translations.id as city_translation_id');
            })->when($order != 'name', function ($collection) use ($order, $order_type) {
                return $collection->orderBy($order, $order_type);
            })->when(isset($request['state_id']), function ($collection) use ($request) {
                return $collection->where('state_id', $request['state_id']);
            })->when(isset($request['has_active_countries']), function ($collection) use ($request) {
                $collection->whereHas('country', function ($q) {
                    $q->where('is_active', 1);
                });
            })->when(isset($request['has_active_states']), function ($collection) use ($request) {
                $collection->whereHas('state', function ($q) {
                    $q->where('is_active', 1);
                });
            });

        return $this;
    }
    
}

