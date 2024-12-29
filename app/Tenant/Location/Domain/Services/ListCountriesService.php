<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Models\Country;
use App\Tenant\Location\Domain\Filters\CountryFilter;
use App\Tenant\Location\Domain\Resources\CountryLiteResource;
use Symfony\Component\HttpFoundation\Response;

class ListCountriesService extends Service
{
    protected $country, $filter;

    public function __construct(Country $country, CountryFilter $filter)
    {
        $this->country = $country;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'order';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'ASC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $active = isset($data['is_active']) ? $data['is_active'] : 1;
        $all = isset($data['all']) ? $data['all'] : 0;
        if (isset($data['is_active']) && $data['is_active'] == 'true')
            $active = 1;
        if (isset($data['is_active']) && $data['is_active'] == 'false')
            $active = 0;
        if (isset($data['is_paginated']) && $data['is_paginated'] == 1):

            $countries = $this->country->whereNull('deleted_at')->orderBy($order, $order_type)->filter($this->filter)
                ->when(isset($data['active']), function ($collection) use ($active) {
                    return $collection->active($active);
                })
                ->when($order == 'name', function ($collection) use ($order_type) {
                    return $collection->join('country_translations', function ($join) {
                        $join->on('countries.id', '=', 'country_translations.country_id')
                            ->where('country_translations.locale', '=', app()->getLocale());
                    })
                        ->groupBy('countries.id')
                        ->orderBy('country_translations.name', $order_type)
                        ->select('countries.*', 'country_translations.id as country_translation_id');
                })
                ->when($order != 'name', function ($collection) use ($order, $order_type) {
                    return $collection->orderBy($order, $order_type);
                })
                ->paginate($limit);

            return [
                'countries' => CountryLiteResource::listCollection($countries),
                'status' => true,
                'message' => 'Countries List',
            ];
        else:
            $countries = $this->country->whereNull('deleted_at')
                ->when(!isset($data['all']) || $all == 0, function ($collection) use ($all) {
//                    return $collection->whereHas('states', function ($q) {
//                        $q->where('is_active', 1)
//                            ->whereHas('cities', function ($q) {
//                                $q->where('is_active', 1);
//                            });
//                    });
                })
                ->whereIsActive($active)->orderBy($order, $order_type)->get();


            // ->when(!auth('admin')->check(), function($collection){
            //     return $collection->whereHas('states', function($q) {
            //         $q->where('is_active', 1);
            //     });
            // });
            // if(auth('admin')->check())
            //     $countries = $countries->orderBy($order, $order_type)->get();
            // else

            return [
                'data' => CountryLiteResource::collection($countries),
                'status' => true,
                'message' => 'Countries List'
            ];
            return new GenericPayload($countries, Response::HTTP_OK,
                ResponseType::CollectionList,
                CountryLiteResource::class);
        endif;
    }
}
