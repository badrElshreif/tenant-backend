<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\ProductView;
use DB;
use Symfony\Component\HttpFoundation\Response;
class ListMost10SellingForStoreProductsService extends Service
{

    public function handle($data = [])
    {
        $products = ProductView::active(1)->where('store_id',auth()->user()->store_id)
        ->whereNotNull('products_view.price')->where('products_view.quantity', '>', 0)
            ->where('products_view.quantity', '>', 0)
        	->leftJoin('order_items', 'product_id', '=', 'products_view.id')
		    ->select('products_view.*', DB::raw('COUNT(order_items.id) as sales_count'), DB::raw('SUM(order_items.quantity) as sales_quantity'))
		    ->groupBy('products_view.id')
		    ->orderBy('sales_quantity', 'desc')
            ->take(10)
		    ->get();
        $res['product']=$products->pluck('name')->toArray();
        $res['qty']=$products->pluck('sales_quantity')->toArray();

        return new GenericPayload($res, Response::HTTP_ACCEPTED);
    }
}
