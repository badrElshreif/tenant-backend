<?php

namespace App\Property\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Property\Domain\Models\Property;
use App\Property\Domain\Filters\PropertyFilter;
use Symfony\Component\HttpFoundation\Response;

class ListPropertiesService extends Service
{
    protected $property, $filter;

    public function __construct(Property $property, PropertyFilter $filter)
    {
        $this->property = $property;
        $this->filter = $filter;
    }

    public function handle($data = []) 
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $query = $this->property->filter($this->filter);
        if( isset($data['is_paginated']) && $data['is_paginated'] == 1 ):
            $properties = $query
            ->when($order == 'name', function($collection) use ($order_type){
                return $collection->join('property_translations', function ($join) {
                    $join->on('properties.id', '=', 'property_translations.property_id')
                        ->where('property_translations.locale', '=', app()->getLocale());
                }) 
                ->groupBy('properties.id')
                ->orderBy('property_translations.name', $order_type)
                ->select('properties.*', 'property_translations.id as property_translation_id');
            })
            ->when($order != 'name', function($collection) use ($order, $order_type){
                return $collection->orderBy($order, $order_type);
            })
            ->paginate(config('app.pagination_limit'));
            return new GenericPayload($properties, Response::HTTP_ACCEPTED);
        else:
            $properties = $query->orderBy($order, $order_type)->get();
            return new GenericPayload($properties, Response::HTTP_OK);   
        endif;
    }
}