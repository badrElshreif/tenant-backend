<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Tenant\Store\Domain\Filters\StoreFilter;
use App\Tenant\Store\Domain\Models\StoreTemp;
use Symfony\Component\HttpFoundation\Response;

class ListStoresTempService extends Service
{
    protected $store, $filter;

    public function __construct(StoreTemp $store, StoreFilter $filter)
    {
        $this->store = $store;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'is_featured';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        if (isset($data['is_paginated']) && $data['is_paginated'] == 1) {
            $stores = $this->store->filter($this->filter)->orderBy($order, $order_type)->paginate($limit);
            return new GenericPayload($stores, Response::HTTP_ACCEPTED);
        }
        $stores = $this->store->filter($this->filter)->active(1)->orderBy('is_featured', 'desc')->get();
        return new GenericPayload($stores, Response::HTTP_OK);
    }
}
