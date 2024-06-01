<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Tenant\Store\Domain\Filters\StoreFilter;
use Symfony\Component\HttpFoundation\Response;

class ListStoresService extends Service
{
    protected $store, $filter;

    public function __construct(Store $store, StoreFilter $filter)
    {
        $this->store = $store;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'is_featured';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $latitude = auth()->check() ? auth()->user()->latitude : $data['latitude'] ?? '';
        $longitude = auth()->check() ? auth()->user()->longitude : $data['longitude'] ?? '';
        if (isset($data['is_paginated']) && $data['is_paginated'] == 1) {
            if (auth()->guard('admin')->check())
                $stores = $this->store->filter($this->filter)->orderBy($order, $order_type)->paginate($limit);
            else
                $stores = $this->store->filter($this->filter)->active(1)
                    ->whereHas('products', function ($q) {
                        $q->where(['is_active'=> 1 ,'approved'=> 1]);
                    })
                    ->when($longitude|| $latitude,function ($query) use ($latitude,$longitude){
                        $query->nearest($latitude, $longitude);
                    })
                    ->orderBy($order, $order_type)->paginate($limit);

            return new GenericPayload($stores, Response::HTTP_ACCEPTED);
        }

        $stores = $this->store->filter($this->filter)
            ->when(!auth()->guard('admin')->check(), function ($collection) {
                return $collection->whereHas('products', function ($q) {
                    $q->where(['is_active'=> 1 ,'approved'=> 1]);
                });
            })->active(1)
            ->when($longitude|| $latitude,function ($query) use ($latitude,$longitude){
                $query->nearest($latitude, $longitude);
            })
            ->orderBy('is_featured', 'desc')
            ->get();
//        $stores = $this->store->filter($this->filter)->active(1)
//            ->when(!auth()->guard('admin')->check(), function ($collection) {
//                return $collection->whereHas('products', function ($q) {
//                    $q->where('is_active', 1);
//                });
//            })
//            ->when($longitude|| $latitude,function ($query) use ($latitude,$longitude){
//                $query->nearest($latitude, $longitude);
//            })
//            ->orderBy('is_featured', 'desc')
//            ->get();
        return new GenericPayload($stores, Response::HTTP_OK);
    }
}
