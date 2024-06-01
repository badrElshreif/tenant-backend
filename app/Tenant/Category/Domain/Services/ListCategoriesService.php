<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Filters\CategoryFilter;
use Symfony\Component\HttpFoundation\Response;

class ListCategoriesService extends Service
{
    protected $category, $filter;

    public function __construct(Category $category, CategoryFilter $filter)
    {
        $this->category = $category;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $type = isset($data['type']) ? $data['type'] : 'stores';
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'order';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'ASC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $active = isset($data['active']) ? $data['active'] : 1;
        $is_paginated = isset($data['is_paginated']) ? $data['is_paginated'] : 1;
        $all = isset($data['all']) ? $data['is_paginated'] : 0;
        if($is_paginated == 'true')
            $is_paginated = 1;

        $categories = $this->category->main()->filter($this->filter)
            ->when($type == 'stores' && !auth('admin')->check(), function($collection){
                return $collection->whereHas('childs');
            });
        if( isset($data['is_paginated']) && $data['is_paginated'] == 0 ):
            $categories = $categories->active(1)
            ->when($type == 'stores' && $data['all'] == 0, function($collection){
                    return $collection->whereHas('childs');
                })
            ->orderBy($order, $order_type)->get();
            return new GenericPayload($categories, Response::HTTP_OK);
        else:
            if( !isset($data['is_paginated'])):
                $categories = $categories->active(1)
//                ->when($type == 'stores', function($collection){
//                    return $collection->whereHas('childs');
//                })
                ->when($type == 'stores' && !auth('store')->check() && !auth('admin')->check(), function($collection){
                    return $collection->whereHas('childs')
                    ->whereHas('childs.products', function($q) {
                        $q->where('is_active', 1);
                    });
                })
                ->orderBy('order', 'ASC');
                return new GenericPayload($categories->get(), Response::HTTP_OK);
            else:
                if( $is_paginated == 0 || $is_paginated == 'false'){
                    //dd($is_paginated);
                    $categories = $categories->active(1)->orderBy('order', 'ASC')->paginate($limit);
                    return new GenericPayload($categories, Response::HTTP_ACCEPTED);
                } else{
                    //dd($is_paginated);
                    $categories = $categories
                        ->when(isset($data['active']), function($collection) use ($active){
                            return $collection->active($active);
                        })
                        ->when($order == 'name', function($collection) use ($order_type){
                            return $collection->join('category_translations', function ($join) {
                                $join->on('categories.id', '=', 'category_translations.category_id')
                                    ->where('category_translations.locale', '=', app()->getLocale());
                            })
                            ->groupBy('categories.id')
                            ->orderBy('category_translations.name', $order_type)
                            ->select('categories.*', 'category_translations.id as category_translation_id');
                        })
                        ->when($order != 'name', function($collection) use ($order, $order_type){
                            return $collection->orderBy($order, $order_type);
                        })
                        ->paginate($limit);
                    return new GenericPayload($categories, Response::HTTP_ACCEPTED);
                }
            endif;
        endif;
    }

}
