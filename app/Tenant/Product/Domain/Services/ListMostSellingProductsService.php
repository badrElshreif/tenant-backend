<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\ProductView;
use DB;
use Symfony\Component\HttpFoundation\Response;
class ListMostSellingProductsService extends Service
{

    public function handle($data = [])
    {
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');

        $products = ProductView::active(1)
        ->whereNotNull('products_view.price')->where('products_view.quantity', '>', 0)
            ->where('products_view.quantity', '>', 0)
            ->when(!auth('store')->check(), function($collection){
                return $collection->where('products_view.approved', true);
            })
            ->whereHas('store', function($qq) {
            $qq->where(['is_active'=> 1 ,'status'=> 1]);
            })
        	->leftJoin('order_items', 'product_id', '=', 'products_view.id')
		    ->select('products_view.*', DB::raw('COUNT(order_items.id) as sales_count'), DB::raw('SUM(order_items.quantity) as sales_quantity'))
		    ->groupBy('products_view.id')
		    ->orderBy('sales_count', 'desc')
		    ->paginate($limit);

        return new GenericPayload($products, Response::HTTP_ACCEPTED);
    }
}
