<?php

namespace App\Tenant\Location\Domain\Repositories;


use App\Tenant\Location\Domain\Filters\CityFilter;
use App\Tenant\Location\Domain\Models\City;

class CityRepository
{
    protected $city;
    protected $filter;

    public function __construct(City $city, CityFilter $filter)
    {
        $this->city = $city;
        $this->filter = $filter;
    }

    public function query($data, $order = 'order', $order_type = 'ASC')
    {
        $this->city = $this->city->whereNull('deleted_at')
//            ->orderBy($order, $order_type)
            ->filter($this->filter)
            ->when($order == 'name', function ($collection) use ($order_type) {
                return $collection->join('city_translations', function ($join) {
                    $join->on('cities.id', '=', 'city_translations.city_id')
                        ->where('city_translations.locale', '=', app()->getLocale());
                })
                    ->groupBy('cities.id')
                    ->orderBy('city_translations.name', $order_type)
                    ->select('cities.*', 'city_translations.id as city_translation_id');
            })->when($order != 'name', function ($collection) use ($order, $order_type) {
                return $collection->orderBy($order, $order_type);
            });

        return $this;
    }

    public function get()
    {
        if (!$this->city) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->city->get();
    }

    public function withTrashed()
    {
        $this->city = $this->city->withTrashed();
        return $this;
    }

    public function onlyTrashed()
    {
        $this->city = $this->city->onlyTrashed();
        return $this;
    }

    public function paginate($limit)
    {
        $limit = $limit ?? config('app.pagination_limit');
        if (!$this->city) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->city->paginate($limit);
    }

    public function getById($id)
    {
        return $this->city->find($id);
    }

    public function create(array $data)
    {
        return $this->city->create($data);
    }

    public function update($id, array $data)
    {
        $city = $this->city->find($id);
        if ($city) {
            $city->update($data);
            return $city;
        }
        return null;
    }

    public function delete($id)
    {
        $city = $this->city->find($id);
        if ($city) {
            $city->delete();
            return true;
        }
        return false;
    }
}
