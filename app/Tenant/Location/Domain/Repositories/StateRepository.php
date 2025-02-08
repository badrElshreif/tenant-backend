<?php

namespace App\Tenant\Location\Domain\Repositories;


use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Location\Domain\Models\State;

class StateRepository extends Repository
{

    public function __construct(State $state)
    {
        parent::__construct($state);
    }

    public function query($request)
    {
        $order = $request['order_by'] ?? 'id';
        $order_type = $request['order_type'] ?? 'ASC';

        $this->model = $this->model
            ->when(isset($request['active']), function ($collection) use ($request) {
                $collection->where('is_active', $request['active']);
            })
            ->when($order == 'name', function ($collection) use ($order_type) {
                return $collection->join('state_translations', function ($join) {
                    $join->on('states.id', '=', 'state_translations.state_id')
                        ->where('state_translations.locale', '=', app()->getLocale());
                })
                    ->groupBy('states.id')
                    ->orderBy('state_translations.name', $order_type)
                    ->select('states.*', 'state_translations.id as state_translation_id');
            })->when($order != 'name', function ($collection) use ($order, $order_type) {
                return $collection->orderBy($order, $order_type);
            })->when(isset($request['country_id']), function ($collection) use ($request) {
                return $collection->where('country_id', $request['country_id']);
            })->when(isset($request['has_active_countries']), function ($collection) use ($request) {
                $collection->whereHas('country', function ($q) {
                    $q->where('is_active', 1);
                });
            });

        return $this;
    }
}
