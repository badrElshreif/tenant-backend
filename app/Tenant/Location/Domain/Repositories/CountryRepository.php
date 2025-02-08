<?php

namespace App\Tenant\Location\Domain\Repositories;


use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Location\Domain\Models\Country;

class CountryRepository extends Repository
{
    protected $country;

    public function __construct(Country $country)
    {
        parent::__construct($country);
    }

    public function filter($request)
    {
        $order = $request['order_by'] ?? 'id';
        $order_type = $request['order_type'] ?? 'ASC';

        $this->model = $this->model
//            ->filter($this->filter)
            ->when(isset($request['active']), function ($collection) use ($request) {
                return $collection->where('is_active', $request['active']);
            })
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


}
