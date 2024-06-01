<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Filters\CategoryFilter;
use Symfony\Component\HttpFoundation\Response;

class ListSubCategoriesService extends Service
{
    protected $category, $filter;

    public function __construct(Category $category, CategoryFilter $filter)
    {
        $this->category = $category;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $active = isset($data['active']) ? $data['active'] : 1;
        $is_detailed = isset($data['is_detailed']) ? $data['is_detailed'] : 1;
        if($is_detailed == 'true')
            $is_detailed = 1;
        $full=$data['full']??false;
        if(isset($data['category_id'])){
            $categories = $this->category->where('parent_id', $data['category_id'])->filter($this->filter)
            ->when((!auth('admin')->check()&&!$full), function($collection){
                return $collection->whereHas('products', function($q) {
                    $q->where('is_active', 1)->where('quantity', '>', 0);
                });
            });
        }else{
            $categories = $this->category->active(1)->whereNotNull('parent_id')->filter($this->filter)
            ->when(!auth('admin')->check(), function($collection){
                return $collection->whereHas('products', function($q) {
                    $q->where('is_active', 1)->where('quantity', '>', 0);
                });
            });
        }

        if( isset($data['is_paginated']) && $data['is_paginated'] == 0 ):
            $categories = $categories->orderBy($order, $order_type)->get();
            return new GenericPayload($categories, Response::HTTP_OK);
        else:
            if( !isset($data['is_detailed'])):
                $categories = $categories->active(1)
                // ->whereHas('products', function($q) {
                //     $q->where('is_active', 1)->where('quantity', '>', 0);
                // })
                ->when(!auth('admin')->check() || !auth('store')->check(), function($collection){
                    return $collection->whereHas('products', function($q) {
                        $q->where('is_active', 1)->where('quantity', '>', 0);
                    });
                })
                ->orderBy('order', 'ASC')->paginate($limit);
                return new GenericPayload($categories, Response::HTTP_OK);
            else:
                if( $is_detailed == 0 || $is_detailed == 'false'){
                    $categories = $categories->active(1)->orderBy('order', 'ASC')->paginate($limit);
                    return new GenericPayload($categories, Response::HTTP_ACCEPTED);
                }
                else{
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
