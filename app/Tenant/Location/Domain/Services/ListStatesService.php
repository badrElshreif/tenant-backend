<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\State;
use App\Tenant\Location\Domain\Filters\StateFilter;
use App\Tenant\Location\Domain\Resources\StateLiteResource;
use Symfony\Component\HttpFoundation\Response;

class ListStatesService extends Service
{
    protected $state, $filter;

    public function __construct(State $state, StateFilter $filter)
    {
        $this->state = $state;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $active = isset($data['is_active']) ? $data['is_active'] : 1;
        $cities = isset($data['cities']) ? $data['cities'] : 0;
        $country_id = $data['country_id'] ?? null;
        if (isset($data['is_active']) && $data['is_active'] == 'true')
            $active = 1;
        if (isset($data['is_active']) && $data['is_active'] == 'false')
            $active = 0;

        if (isset($data['is_paginated']) && $data['is_paginated'] == 1):
            $states = $this->state->filter($this->filter)
                ->when(isset($data['active']), function ($collection) use ($active) {
                    return $collection->active($active);
                })
                ->when($order == 'name', function ($collection) use ($order_type) {
                    return $collection->join('state_translations', function ($join) {
                        $join->on('states.id', '=', 'state_translations.state_id')
                            ->where('state_translations.locale', '=', app()->getLocale());
                    })
                        ->groupBy('states.id')
                        ->orderBy('state_translations.name', $order_type)
                        ->select('states.*', 'state_translations.id as state_translation_id');
                })
                ->when($order != 'name', function ($collection) use ($order, $order_type) {
                    return $collection->orderBy($order, $order_type);
                })
                ->when(isset($country_id), function ($collection) use ($country_id) {
                    return $collection->where('country_id', $country_id);
                })
                ->whereHas('country', function ($q) {
                    $q->where('is_active', 1);
                })
                ->paginate($limit);
            return [
                'data' => StateLiteResource::listCollection($cities),
                'status' => true,
                'message' => 'States List',
            ];
        else:
            $states = $this->state->filter($this->filter)->active(1)
                ->whereHas('country', function ($q) {
                    $q->where('is_active', 1);
                })
                ->when(isset($country_id), function ($collection) use ($country_id) {
                    return $collection->where('country_id', $country_id);
                })
//                ->when(!auth('admin')->check() || $cities == 1, function ($collection) {
//                    return $collection->whereHas('cities', function ($q) {
//                        $q->where('is_active', 1);
//                    });
//                })
                ->get();

            return [
                'data' => StateLiteResource::listCollection($cities),
                'status' => true,
                'message' => 'States List',
            ];
        endif;
    }
}
