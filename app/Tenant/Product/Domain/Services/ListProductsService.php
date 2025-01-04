<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\ProductView;
use App\Tenant\Product\Domain\Filters\ProductFilter;
use App\Tenant\Product\Domain\Resources\ProductLiteResource;
use DB;
use Symfony\Component\HttpFoundation\Response;

class ListProductsService extends Service
{
    protected $product, $filter;

    public function __construct(ProductView $product, ProductFilter $filter)
    {
        $this->product = $product;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        //Log::info("filter",$data);

        try {
            $storeNearest = [];
            $store_id = null;

            $data['type'] = $data['type'] ?? "stores";

            if (auth('tenant-admin')->check()) {
                $storesIDS = \Illuminate\Support\Facades\DB::table('stores')->pluck('id');
            } else if (auth('tenant-store')->check()) {
                $storesIDS = \Illuminate\Support\Facades\DB::table('stores')->where('seller_id', auth()->guard('tenant-store')->id() ?? auth()->guard('center')->id())->pluck('id');
            } else {

                $storesIDS = \Illuminate\Support\Facades\DB::table('stores');
                if (auth()->check()) {
                    //Log::info("auth",[auth()->user()]);
                    /*    $latitude = auth()->user()->latitude;
                        $longitude = auth()->user()->longitude;

                          $haversine = "(
                            6371 * acos(
                                cos(radians(" .$latitude. "))
                                * cos(radians(`latitude`))
                                * cos(radians(`longitude`) - radians(" .$longitude. "))
                                + sin(radians(" .$latitude. ")) * sin(radians(`latitude`))
                            )
                        )";

                        $storesIDS = $storesIDS->select("id")->selectRaw("$haversine AS distance");*/
                    //  ->having("distance", "<=", 25);
                }
                if (isset($data['city_id'])) {
                    $storesIDS = $storesIDS->where('is_active', 1)->where('city_id', $data['city_id']);
                } elseif (isset($data['state_id'])) {
                    $storesIDS = $storesIDS->where('is_active', 1)->where('state_id', $data['state_id']);
                } elseif (isset($data['country_id'])) {
                    $storesIDS = $storesIDS->where('is_active', 1)->where('country_id', $data['country_id']);
                } else {
                    $storesIDS = $storesIDS->where('is_active', 1);
                }

                /* if(auth()->check()){
                       $arr = $storesIDS->orderBy('distance', 'asc')->get();
                      $storesIDS = $storesIDS->orderBy('distance', 'asc')->pluck('id');
                      foreach($arr as $storesID){
                            $d = (object)['id'=>$storesID->id,'distance' => $storesID->distance];
                            array_push($storeNearest,$d);
                      }

                    // Log::info("nearest",$storeNearest);
                 }*/

                $storesIDS = $storesIDS->pluck('id');
            }


            // $store_id = auth()->user()->store_id;

            $order = isset($data['orderBy']) ? $data['orderBy'] : 'id';
            $order_type = isset($data['orderType']) ? $data['orderType'] : 'DESC';
            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            $active = isset($data['active']) ? $data['active'] : 1;
            $is_detailed = isset($data['is_detailed']) ? $data['is_detailed'] : 1;


            $category = $data['category'] ?? null;
            if ($is_detailed == 'true')
                $is_detailed = 1;


            if (auth('api')->check() || auth('web')->check()) {
                $latitude = auth()->user()->latitude;
                $longitude = auth()->user()->longitude;

                $haversine = "(
                    6371 * acos(
                        cos(radians(" . $latitude . "))
                        * cos(radians(stores.`latitude`))
                        * cos(radians(stores.`longitude`) - radians(" . $longitude . "))
                        + sin(radians(" . $latitude . ")) * sin(radians(stores.`latitude`))
                    )
                )";

                $products = $this->product
                    ->leftJoin('stores', function ($join) {
                        $join->on('stores.id', '=', 'products_view.store_id');
                    })->select('products_view.*', DB::raw("$haversine AS distance"))
                    ->orderBy('distance', 'asc');

            } else {
                $products = $this->product;
            }


            $products = $products
//                ->whereRelation('category', 'type') // $data['type']
                ->when($store_id, function ($collection) use ($store_id) {
                    return $collection->where('products_view.store_id', $store_id);
                })
                ->when(!auth('tenant-store')->check(), function ($collection) use ($store_id, $active) {
                    return $collection->where('products_view.approved', $active);
                })
                /*->when($category, function($collection) use ($category){
                return $collection->where('products_view.category_id', $category);
            })*/
                ->when(!isset($store_id), function ($collection) use ($storesIDS) {
                    info("storesIDS", [$storesIDS]);
                    if (empty($storesIDS)) {
                        return $collection->whereNull('products_view.store_id');
                    }
                    return $collection->whereIn('products_view.store_id', $storesIDS);
                })
                ->filter($this->filter)
                ->when($order == 'name', function ($collection) use ($order_type) {
                    return $collection->join('product_translations', function ($join) {
                        $join->on('products_view.id', '=', 'product_translations.product_id')
                            ->where('product_translations.locale', '=', app()->getLocale());
                    })
                        ->groupBy('products_view.id')
                        ->orderBy('product_translations.name', $order_type)
                        ->select('products_view.*', 'product_translations.id as product_translation_id');
                })
                ->when($order == 'most_selling', function ($collection) use ($order_type) {
                    return $collection->leftJoin('order_items', 'product_id', '=', 'products_view.id')
                        ->select('products_view.*', DB::raw('COUNT(order_items.id) as sales_count')
                            , DB::raw('SUM(order_items.quantity) as sales_quantity'))
                        ->groupBy('products_view.id')
                        ->orderBy('sales_count', $order_type);
                })
                ->when($order == 'most_rated', function ($collection) use ($order_type) {
                    return $collection->leftJoin('ratings', 'product_id', '=', 'products_view.id')
                        ->select('products_view.*', DB::raw('avg(ratings.rate) as average'))
                        ->groupBy('products_view.id')
                        ->orderBy('average', $order_type);
                })->when($order != 'name' && $order != 'most_selling' && $order != 'most_rated', function ($collection) use ($order, $order_type) {
                    return $collection->orderBy($order, $order_type);
                });
            if (!isset($data['is_detailed'])):
                $products = $products->where('products_view.is_active', 1)->whereNotNull('products_view.price')
                    ->when($data['type'] == 'stores', function ($collection) {
                        return $collection->where('products_view.quantity', '>', 0)
                            ->whereRelation('brand', 'is_active', 1);
                    })
                    ->whereRelation('category', 'is_active', 1)
                    ->paginate($limit);


                foreach ($products as $k => $product) {
                    $products[$k]->distance = (string)round($product->distance, 1);
                    /*  $products[$k]->distance = (String) round($storeNearestCollect->first(function($item) use($product) {
                               return $product->store_id == $item->id;
                           })->distance,2); */
                }

                //info($products);
                return [
                    'data' => ProductLiteResource::listCollection($products),
                    'status' => true,
                    'message' => __('success.listedSuccessfully'),
                ];
            else:
                if (isset($data['has_pagination'])) {
                    $products = $products->where('products_view.is_active', $active)->get();
                    return [
                        'data' => ProductLiteResource::collection($products),
                        'status' => true,
                        'message' => __('success.listedSuccessfully'),
                    ];
                }
                $products = $products->when(isset($data['active']), function ($collection) use ($active) {
                    return $collection->where('products_view.is_active', $active);
                })
                    ->paginate($limit);
                return [
                    'data' => ProductLiteResource::collection($products),
                    'status' => true,
                    'message' => __('success.listedSuccessfully'),
                ];
            endif;
        } catch (\Exception $e) {
            info("list products", ['error' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }
    }
}
