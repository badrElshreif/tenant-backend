<?php

namespace App\Tenant\AppContent\Domain\Services\Statistics;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Order\Domain\Models\Order;
use App\Product\Domain\Models\ProductView;
use Symfony\Component\HttpFoundation\Response;
use App\Category\Domain\Models\Category;
use App\Product\Domain\Models\Product;
use App\Property\Domain\Models\Property;
use App\Warranty\Domain\Models\Warranty;
use App\Brand\Domain\Models\Brand;
use App\Store\Domain\Models\Store;
use App\User\Domain\Models\Customer;
use Illuminate\Support\Arr;

class GetStatisticsService extends Service
{

    public function handle($data = [])
    {
        $statistics = [];
        $store_id = auth()->user()->store_id;
        $statistics['products'] = Product::
            whereHas('store', function($query) {
            $query->where('status', 1);})->
            active(1)
            ->where("approved",1)
            ->whereRelation('category', 'type', 'stores')
            ->when($store_id, function($collection) use ($store_id){
                return $collection->where('products.store_id', $store_id);
            })
            ->count();
        $statistics['not_active_products'] = Product::where(["is_active"=> 0 , "approved"=>0])
            ->whereRelation('category', 'type', 'stores')
            ->when($store_id, function($collection) use ($store_id){
                return $collection->where('products.store_id', $store_id);
            })
            ->count();

        $statistics['services'] = Product::active(1)
            ->whereRelation('category', 'type', 'centers')
            ->when($store_id, function($collection) use ($store_id){
                return $collection->where('products.store_id', $store_id);
            })
            ->count();

        $statistics['categories'] = Category::active(1)->whereNull('parent_id')->whereType('stores')->count();
        $statistics['service_types'] = Category::active(1)->whereType('centers')->count();
        $statistics['stores'] = Store::active(1)->whereType('stores')->count();
        $statistics['centers'] = Store::active(1)->whereType('centers')->count();
        $statistics['stores_update_request'] = Store::where('is_active', 0)->whereType('stores')->count();
        $statistics['new_stores_request'] = Store::where('status', 0)->whereType('stores')->count();
        $statistics['edit_products_request'] = ProductView::where('approved', 0)->count();
        $statistics['product_orders'] = Order::whereHas('status', function($q)  {
            // $q->where('key', 'like', '%' . $status . '%');
            $q->whereIn('key',['new', 'accepted', 'delivering', 'ready_for_delivery']);
        })->count();

        $statistics['warranties'] = Warranty::active(1)->when($store_id, function($collection) use ($store_id){
                return $collection->where('warranties.store_id', $store_id);
            })
        ->count();
        // $statistics['users'] = Customer::where('is_active', 1)->count();
        $statistics['users'] = Customer::all()->count();
        if(auth()->guard('store')->check()){
            return new GenericPayload(Arr::only($statistics, ['categories', 'products', 'warranties']), Response::HTTP_RESET_CONTENT);
        } else if(auth()->guard('center')->check()) {
            return new GenericPayload(Arr::only($statistics, ['service_types', 'services']), Response::HTTP_RESET_CONTENT);
        }else  if(auth()->guard('admin')->check()){
            return new GenericPayload($statistics, Response::HTTP_RESET_CONTENT);
        } else {
            return new GenericPayload([], Response::HTTP_RESET_CONTENT);
        }

    }
}


