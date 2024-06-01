<?php

namespace App\Tenant\Offer\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Offer\Domain\Models\Offer;
use App\Tenant\Offer\Domain\Filters\OfferFilter;
use Symfony\Component\HttpFoundation\Response;

class ListOffersService extends Service
{
    protected $offer, $filter;

    public function __construct(Offer $offer, OfferFilter $filter)
    {
        $this->offer = $offer;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $active = isset($data['active']) ? $data['active'] : 1;
        $store_id = null;
        if(auth()->guard('store')->check())
            $store_id = auth()->user()->store_id;

        if( isset($data['is_paginated']) && $data['is_paginated'] == 0 ):
            $products = $this->offer->active()->filter($this->filter)->orderBy($order, $order_type)->get();
            return new GenericPayload($products, Response::HTTP_OK);
        else:
            $offers = $this->offer
            ->filter($this->filter)
            ->when(isset($data['active']), function($collection) use ($active){
                return $collection->status($active);
            })
            ->when($store_id, function($collection) use ($store_id){
                return $collection->where('offers.store_id', $store_id);
            })
            ->when($order == 'name', function($collection) use ($order_type){
                return $collection->join('offer_translations', function ($join) {
                    $join->on('offers.id', '=', 'offer_translations.offer_id')
                        ->where('offer_translations.locale', '=', app()->getLocale());
                })
                ->groupBy('offers.id')
                ->orderBy('offer_translations.name', $order_type)
                ->select('offers.*', 'offer_translations.id as offer_translation_id');
            })
            ->when($order != 'name', function($collection) use ($order, $order_type){
                return $collection->orderBy($order, $order_type);
            })
            ->paginate($limit);
            return new GenericPayload($offers, Response::HTTP_ACCEPTED);
        endif;
    }
}
