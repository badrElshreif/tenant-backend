<?php

namespace App\Tenant\Brand\Domain\Repositories;


use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Brand\Domain\Models\Brand;

class BrandRepository extends Repository
{

    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
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
                return $collection->join('brand_translations', function ($join) {
                    $join->on('brands.id', '=', 'brand_translations.brand_id')
                        ->where('brand_translations.locale', '=', app()->getLocale());
                })
                    //  ->groupBy('countries.id')
                    ->orderBy('brand_translations.name', $order_type)
                    ->select('brands.*', 'brand_translations.id as brand_translation_id');
            })->when($order != 'name', function ($collection) use ($order, $order_type) {
                return $collection->orderBy($order, $order_type);
            });

        return $this;
    }


}
