<?php

namespace App\Tenant\Location\Domain\Repositories;


use App\Tenant\Location\Domain\Filters\CountryFilter;
use App\Tenant\Location\Domain\Models\Country;

class CountryRepository
{
    protected $country;
    protected $filter;

    public function __construct(Country $country, CountryFilter $filter)
    {
        $this->country = $country;
        $this->filter = $filter;
    }

    public function query($request)
    {
        $order = $request['order_by'] ?? 'order';
        $order_type = $request['order_type'] ?? 'ASC';

        $this->country = $this->country
           // ->filter($this->filter)
            ->when($order == 'name', function ($collection) use ($order_type) {
                return $collection->join('country_translations', function ($join) {
                    $join->on('countries.id', '=', 'country_translations.country_id')
                        ->where('country_translations.locale', '=', app()->getLocale());
                })
                    //  ->groupBy('countries.id')
                    ->orderBy('country_translations.name', $order_type)
                    ->select('countries.*', 'country_translations.id as country_translation_id');
            })->when($order != 'name', function ($collection) use ($order, $order_type) {
                return $collection->orderBy($order, $order_type);
            });

        return $this;
    }

    public function withTrashed()
    {
        $this->country = $this->country->withTrashed();
        return $this;
    }

    public function onlyTrashed()
    {
        $this->country = $this->country->onlyTrashed();
        return $this;
    }

    public function get()
    {
        if (!$this->country) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->country->get();
    }

    public function paginate($limit)
    {
        $limit = $limit ?? config('app.pagination_limit');
        if (!$this->country) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->country->paginate($limit);
    }

    public function getById($id)
    {
        return $this->country->find($id);
    }

    public function create(array $data)
    {
        return $this->country->create($data);
    }

    public function update($id, array $data)
    {
        $country = $this->country->find($id);
        if ($country) {
            $country->update($data);
            return $country;
        }
        return null;
    }

    public function delete($id)
    {
        $country = $this->country->find($id);
        if ($country) {
            $country->delete();
            return true;
        }
        return false;
    }
}
