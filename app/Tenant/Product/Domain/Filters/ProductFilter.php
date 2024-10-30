<?php

namespace App\Tenant\Product\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class ProductFilter extends QueryFilter
{

    public function status($is_active)
    {
        //$this->builder->where('is_active', $is_active);
        //$this->builder->active($is_active);
        if ($is_active == 0) {
            $this->builder->where('is_active', '<>', 1)
                ->orWhere([
                    ['is_active', 1],
                    ['deactivation_start_date', '<=', date('Y-m-d')],
                    ['deactivation_end_date', '>=', date('Y-m-d')]
                ]);
        } else {
            $this->builder->where([
                ['is_active', 1],
                ['deactivation_start_date', null],
                ['deactivation_end_date', null]
            ])
                ->orWhere([
                    ['is_active', 1],
                    ['deactivation_start_date', '>', date('Y-m-d')],
                    ['deactivation_end_date', '<', date('Y-m-d')]
                ]);
        }
    }

    public function name($name)
    {
        $this->builder->whereHas('translations', function ($q) use ($name) {
            $q->where('name', 'like', '%' . $name . '%');
        });
    }

    public function quantityFrom($quantity)
    {
        $this->builder->where('quantity', '>=', $quantity);
    }

    public function quantityTo($quantity)
    {
        $this->builder->where('quantity', '<=', $quantity);
    }

    public function quantity($quantity)
    {
        $this->builder->where('quantity', $quantity);
    }

    public function barcode($barcode)
    {
        $this->builder->where('barcode', 'like', '%' . $barcode . '%');
    }

    public function category($category_id)
    {
        // $this->builder->where('category_id', $category_id)
        // ->orWhereHas('category', function($q) use ($category_id){
        //     $q->where('parent_id', $category_id);
        // });
        $this->builder->where(function ($query) use ($category_id) {
            $query->where('category_id', $category_id)
                ->orWhereHas('category', function ($q) use ($category_id) {
                    $q->where('parent_id', $category_id);
                });
        });
    }

    public function subCategory($category_id)
    {
        $this->builder->where('category_id', $category_id);
    }

    public function previewFees($preview_fees)
    {
        $this->builder->where('preview_fees', $preview_fees);
    }

    public function store($store_id)
    {
        $this->builder->where('store_id', $store_id);
    }

    public function brand($brand_id)
    {
        //dd($brand_id);
        $this->builder->where('brand_id', $brand_id);
        // $this->builder->whereHas('brand', function($q) use ($brand_id) {
        //     $q->where('id', $brand_id);
        // });
    }

    public function rating($rate)
    {
        $this->builder->whereHas('ratings', function ($q) use ($rate) {
            //$q->selectRaw('avg(rate) as product_rate')->having('product_rate', '>=', $rate);
            $q->havingRaw('avg(rate) >= ' . $rate);
        });
    }

    public function price($price_range)
    {
        $range = array_map('intval', explode(',', $price_range));
        $this->builder->when((count($range) > 1), function ($q) use ($range) {
            return $q->where('price', '>=', $range[0])
                ->where('price', '<=', $range[1]);
        }, function ($query) use ($price_range) {
            return $query->where('price', $price_range);
        });
    }

    public function priceIncludingTax($price_range)
    {
        $range = array_map('intval', explode(',', $price_range));
        $this->builder->when((count($range) > 1), function ($q) use ($range) {
            return $q->where('price_including_tax', '>=', $range[0])
                ->where('price_including_tax', '<=', $range[1]);
        }, function ($query) use ($price_range) {
            return $query->where('price_including_tax', $price_range);
        });
    }

    public function priceFrom($price)
    {
        $this->builder->where('price_including_tax', '>=', $price);
    }

    public function priceTo($price)
    {
        $this->builder->where('price_including_tax', '<=', $price);
    }

    public function brands($brand_ids)
    {
        // $ids = array_map('intval', explode(',', $brand_ids));
        // $this->builder->whereIn('brand_id', $ids);
        $ids = [];
        $ids = json_decode($brand_ids);
        if (!is_array($ids))
            $ids = json_decode($ids);
        $this->builder->whereIn('brand_id', $ids);
    }

    public function ids($product_ids)
    {
        // $ids = array_map('intval', explode(',', $ids));
        // $this->builder->whereIn('id', $ids);
        $ids = [];
        $ids = json_decode($product_ids);
        if (!is_array($ids))
            $ids = json_decode($ids);
        $this->builder->whereIn('id', $ids);
    }

    public function subCategories($category_ids)
    {
        // $ids = array_map('intval', explode(',', $category_ids));
        // $this->builder->whereIn('category_id', $ids);
        $ids = [];
        $ids = json_decode($category_ids);
        if (!is_array($ids))
            $ids = json_decode($ids);
        $this->builder->whereIn('category_id', $ids);
    }

    public function subcategoriesFilter($category_ids)
    {
        // dd($category_ids);
        $ids = [];
        $ids = json_decode($category_ids);
        if (!is_array($ids))
            $ids = json_decode($ids);
        $this->builder->whereIn('category_id', $ids);
    }

    public function publicSearch($search)
    {
        //dd($search);
        // $this->builder->whereHas('translations', function($q) use ($search) {
        //     $q->where('name', 'like', '%' . $search . '%');
        // })
        // ->orWhereHas('category', function($category) use ($search){
        //     $category->whereHas('translations', function($q) use ($search) {
        //         $q->where('name', 'like', '%' . $search . '%');
        //     })
        //     ->orWhereHas('parent', function($parent_category) use ($search){
        //         $parent_category->whereHas('translations', function($q) use ($search) {
        //             $q->where('name', 'like', '%' . $search . '%');
        //         });
        //     });
        // })
        // ->orWhereHas('brand', function($brand) use ($search){
        //     $brand->whereHas('translations', function($q) use ($search) {
        //         $q->where('name', 'like', '%' . $search . '%');
        //     });
        // })
        // ->orWhereHas('store', function($store) use ($search){
        //     $store->whereHas('translations', function($q) use ($search) {
        //         $q->where('name', 'like', '%' . $search . '%');
        //     });
        // })
        // ->orWhere('barcode','like', '%'.$search.'%')
        // ->orWhere('price', 'like', '%'.$search.'%')
        // ->orWhere('preview_fees', 'like', '%'.$search.'%');

        $this->builder->where(function ($query) use ($search) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
                ->orWhereHas('category', function ($category) use ($search) {
                    $category->whereHas('translations', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                        ->orWhereHas('parent', function ($parent_category) use ($search) {
                            $parent_category->whereHas('translations', function ($q) use ($search) {
                                $q->where('name', 'like', '%' . $search . '%');
                            });
                        });
                })
                ->orWhereHas('brand', function ($brand) use ($search) {
                    $brand->whereHas('translations', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                })
                ->orWhereHas('store', function ($store) use ($search) {
                    $store->whereHas('translations', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                })
                ->orWhere('barcode', 'like', '%' . $search . '%')
                ->orWhere('price', 'like', '%' . $search . '%')
                ->orWhere('preview_fees', 'like', '%' . $search . '%');
        });
    }

    // public function orderBy($orderBy)
    // {
    //     $this->builder->when($orderBy != 'name', function($collection) use ($orderBy){
    //         return $collection->orderBy($orderBy, request()->orderType ?? 'DESC');
    //     })
    //     ->when($orderBy == 'name', function($collection) {
    //         return $collection->orderBy('product_translations.name', request()->orderType ?? 'DESC')
    //         ->where('product_translations.locale', app()->getLocale());
    //     });
    // }


}

