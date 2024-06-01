<?php

namespace App\Tenant\Offer\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Product\Domain\Models\ProductView;
use Symfony\Component\HttpFoundation\Response;
use App\Product\Domain\Filters\ProductFilter;
use DB;

class ListOfferProductsService extends Service
{
	protected $product, $filter;

    public function __construct(ProductView $product, ProductFilter $filter)
    {
        $this->product = $product;
        $this->filter = $filter;
    }
    public function handle($data = [])
    {
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
        $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';


        $products = $this->product
            ->whereRelation('category', 'type','stores')
            ->has('offer')->with('offer')
            ->filter($this->filter)
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
            })
            ->where('products_view.is_active', 1)->whereNotNull('products_view.price')
            ->where('products_view.quantity', '>', 0)
            ->whereRelation('brand', 'is_active', 1)
            ->whereRelation('category', 'is_active', 1)
            ->paginate($limit);
            return new GenericPayload($products, Response::HTTP_ACCEPTED);
    }
}
