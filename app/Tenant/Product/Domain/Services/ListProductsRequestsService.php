<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Location\Domain\Models\City;
use App\Location\Domain\Models\State;
use App\Tenant\Product\Domain\Models\ProductView;
use App\Tenant\Product\Domain\Models\Product;
use App\Tenant\Product\Domain\Filters\ProductFilter;
use App\Store\Domain\Models\Store;
use DB;
use Symfony\Component\HttpFoundation\Response;
class ListProductsRequestsService extends Service
{
    protected $product, $filter;

    public function __construct(ProductView $product, ProductFilter $filter)
    {
        $this->product = $product;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $store_id = null;
        if(auth()->guard('store')->check() || auth()->guard('center')->check()){
            $storesIDS=\Illuminate\Support\Facades\DB::table('stores')->where('seller_id',auth()->guard('store')->id()??auth()->guard('center')->id())->pluck('id');

        }else{
            if(isset($data['city_id']))
                $storesIDS=\Illuminate\Support\Facades\DB::table('stores')->where('is_active',1)->where('city_id',$data['city_id'])->pluck('id');
            elseif(isset($data['state_id']))
                $storesIDS=\Illuminate\Support\Facades\DB::table('stores')->where('is_active',1)->where('state_id',$data['state_id'])->pluck('id');
            elseif (isset($data['country_id']))
                $storesIDS=\Illuminate\Support\Facades\DB::table('stores')->where('is_active',1)->where('country_id',$data['country_id'])->pluck('id');
            else
                $storesIDS=\Illuminate\Support\Facades\DB::table('stores')->where('is_active',1)->pluck('id');

        }
           // $store_id = auth()->user()->store_id;

        $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $active = isset($data['active']) ? $data['active'] : 1;
        $is_detailed = isset($data['is_detailed']) ? $data['is_detailed'] : 1;

         $category=$data['category']??null;
        if($is_detailed == 'true')
            $is_detailed = 1;
            $products = $this->product
            ->whereRelation('category', 'type', $data['type'])
            ->when($store_id, function($collection) use ($store_id){
                return $collection->where('products_view.store_id', $store_id);
            })
                ->where('products_view.approved', false)
                /*->when($category, function($collection) use ($category){
                return $collection->where('products_view.category_id', $category);
            })*/->when(!isset($store_id), function($collection) use ($storesIDS){
                return $collection->whereIn('products_view.store_id',$storesIDS);
            })->filter($this->filter)
            ->when($order == 'name', function($collection) use ($order_type){
                return $collection->join('product_translations', function ($join) {
                    $join->on('products_view.id', '=', 'product_translations.product_id')
                        ->where('product_translations.locale', '=', app()->getLocale());
                })
                ->groupBy('products_view.id')
                ->orderBy('product_translations.name', $order_type)
                ->select('products_view.*', 'product_translations.id as product_translation_id');
            })
            ->when($order == 'most_selling', function($collection) use ($order_type){
                return $collection->leftJoin('order_items', 'product_id', '=', 'products_view.id')
                    ->select('products_view.*', DB::raw('COUNT(order_items.id) as sales_count')
                        , DB::raw('SUM(order_items.quantity) as sales_quantity'))
                    ->groupBy('products_view.id')
                    ->orderBy('sales_count', $order_type);
            })
            ->when($order == 'most_rated', function($collection) use ($order_type){
                return $collection->leftJoin('ratings', 'product_id', '=', 'products_view.id')
                    ->select('products_view.*', DB::raw('avg(ratings.rate) as average'))
                    ->groupBy('products_view.id')
                    ->orderBy('average', $order_type);
            })
            ->when($order != 'name' && $order != 'most_selling' && $order != 'most_rated', function($collection) use ($order, $order_type){
                return $collection->orderBy($order, $order_type);
            });
        if( !isset($data['is_detailed'])):
            $products = $products->where('products_view.is_active', 1)->whereNotNull('products_view.price')
            ->when($data['type'] == 'stores', function($collection) {
                return $collection->where('products_view.quantity', '>', 0)
                ->whereRelation('brand', 'is_active', 1);
            })
            ->whereRelation('category', 'is_active', 1)
            ->paginate($limit);
            return new GenericPayload($products, Response::HTTP_ACCEPTED);
        else:
            if(isset($data['has_pagination'])){
                $products = $products->where('products_view.is_active', $active)->get();
                return new GenericPayload($products, Response::HTTP_OK);
            }
            $products = $products->when(isset($data['active']), function($collection) use ($active){
                return $collection->where('products_view.is_active', $active);
            })
            ->paginate($limit);
            return new GenericPayload($products, Response::HTTP_ACCEPTED);
        endif;
    }
}
