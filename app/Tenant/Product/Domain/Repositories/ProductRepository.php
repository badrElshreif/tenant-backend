<?php

namespace App\Tenant\Product\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Product\Domain\Models\Product;
use App\Tenant\Product\Domain\Models\ProductView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductRepository extends Repository
{
    private const DEFAULT_ORDER_COLUMN = 'id';
    private const DEFAULT_ORDER_TYPE = 'DESC';
    private const DEFAULT_PER_PAGE = 15;

    private Product $product;
    private ProductView $productView;

    /**
     * Create a new ProductRepository instance.
     *
     * @param Product $product
     * @param ProductView $productView
     */
    public function __construct(Product $product, ProductView $productView)
    {
        $this->product = $product;
        $this->productView = $productView;
        parent::__construct($product);
    }

    /**
     * Get store IDs based on authentication and filters.
     *
     * @param array $data
     * @return array
     */
    private function getStoreIds(array $data): array
    {
        if (auth('tenant-admin')->check()) {
            return DB::table('stores')->pluck('id')->toArray();
        }

        if (auth('tenant-store')->check()) {
            return DB::table('stores')
                ->where('seller_id', auth()->guard('tenant-store')->id() ?? auth()->guard('center')->id())
                ->pluck('id')
                ->toArray();
        }

        $query = DB::table('stores')->where('is_active', 1);

        if (isset($data['city_id'])) {
            $query->where('city_id', $data['city_id']);
        } elseif (isset($data['state_id'])) {
            $query->where('state_id', $data['state_id']);
        } elseif (isset($data['country_id'])) {
            $query->where('country_id', $data['country_id']);
        }

        return $query->pluck('id')->toArray();
    }

    /**
     * Add distance calculation to query if user has location.
     *
     * @param Builder $query
     * @return Builder
     */
    private function addDistanceCalculation(Builder $query,$latitude,$longitude): Builder
    {

        $haversine = "(
            6371 * acos(
                cos(radians({$latitude}))
                * cos(radians(stores.`latitude`))
                * cos(radians(stores.`longitude`) - radians({$longitude}))
                + sin(radians({$latitude})) * sin(radians(stores.`latitude`))
            )
        )";

        return $query->leftJoin('stores', 'stores.id', '=', 'products_view.store_id')
            ->select('products_view.*', DB::raw("{$haversine} AS distance"))
            ->orderBy('distance', 'asc');
    }

    /**
     * Build query with filters and sorting
     *
     * @param array $request
     * @return self
     */
    public function filter(array $request)
    {
        $orderColumn = $request['order_by'] ?? self::DEFAULT_ORDER_COLUMN;
        $orderType = $request['order_type'] ?? self::DEFAULT_ORDER_TYPE;
        $limit = $request['per_page'] ?? config('app.pagination_limit');
        $active = $request['active'] ?? 1;
        $type = $request['type'] ?? 'stores';
        $isDetailed = $request['is_detailed'] ?? false;

        // Get store IDs based on authentication and filters
        $storeIds = $this->getStoreIds($request);

        // Build base query
        $query = $this->productView
            ->when(
                isset($request['store_id']),
                fn (Builder $query) => $query->where('products_view.store_id', $request['store_id'])
            )
            ->when(
                !auth('tenant-store')->check(),
                fn (Builder $query) => $query->where('products_view.approved', $active)
            )
            ->when(
                !empty($storeIds),
                fn (Builder $query) => $query->whereIn('products_view.store_id', $storeIds),
                fn (Builder $query) => $query->whereNull('products_view.store_id')
            )
            ->when(
                isset($request['category_id']),
                fn (Builder $query) => $query->where('products_view.category_id', $request['category_id'])
            )
            ->when(
                isset($request['brand_id']),
                fn (Builder $query) => $query->where('products_view.brand_id', $request['brand_id'])
            )
            ->when(
                isset($request['price_from']),
                fn (Builder $query) => $query->where('products_view.price', '>=', $request['price_from'])
            )
            ->when(
                isset($request['price_to']),
                fn (Builder $query) => $query->where('products_view.price', '<=', $request['price_to'])
            )
            ->when(
                !$isDetailed,
                fn (Builder $query) => $query->where('products_view.is_active', 1)
                    ->whereNotNull('products_view.price')
                    ->when($type === 'stores', function ($query) {
                        return $query->where('products_view.quantity', '>', 0)
                            ->whereRelation('brand', 'is_active', 1);
                    })
                    ->whereRelation('category', 'is_active', 1)
            )
            ->when(
                isset($request['search']),
                fn (Builder $query) => $this->searchByName($query, $request['search'])
            );

             // Add distance calculation if user has location
            if(!empty($request['latitude']) && !empty($request['longitude'])){
                $query = $this->addDistanceCalculation($query, $request['latitude'], $request['longitude']);
            }

        // Handle special ordering cases
        $query = $this->handleSpecialOrdering($query, $orderColumn, $orderType);

        // Set pagination if not detailed view


            // Format distance for each product if available
          /*  $query->each(function ($product) {
                if (isset($product->distance)) {
                    $product->distance = (string)round($product->distance, 1);
                }
            }); */


        $this->model = $query;
        return $this;
    }

    /**
     * Handle special ordering cases (name, most_selling, most_rated)
     *
     * @param Builder $query
     * @param string $orderColumn
     * @param string $orderType
     * @return Builder
     */
    private function handleSpecialOrdering(Builder $query, string $orderColumn, string $orderType): Builder
    {

        return match($orderColumn) {
            'name' => $this->orderByTranslatedName($query, $orderType),
            'most_selling' => $query->leftJoin('order_items', 'product_id', '=', 'products_view.id')
                ->select('products_view.*', DB::raw('COUNT(order_items.id) as sales_count, SUM(order_items.quantity) as sales_quantity'))
                ->groupBy('products_view.id')
                ->orderBy('sales_count', $orderType),
            'most_rated' => $query->leftJoin('ratings', 'product_id', '=', 'products_view.id')
                ->select('products_view.*', DB::raw('avg(ratings.rate) as average'))
                ->groupBy('products_view.id')
                ->orderBy('average', $orderType),
            default => $query->orderBy($orderColumn, $orderType)
        };
    }

    /**
     * Search products by name in translations
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    private function searchByName(Builder $query, string $search): Builder
    {
        return $query->join('product_translations', function ($join) use ($search) {
            $join->on('products_view.id', '=', 'product_translations.product_id')
                ->where('product_translations.locale', '=', app()->getLocale())
                ->where('product_translations.name', 'like', "%{$search}%");
        });
    }

    /**
     * Order query by translated name
     *
     * @param Builder $query
     * @param string $orderType
     * @return Builder
     */
    private function orderByTranslatedName(Builder $query, string $orderType): Builder
    {
        return $query->join('product_translations', function ($join) {
                $join->on('products_view.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', '=', app()->getLocale());
            })
            ->groupBy('products_view.id', 'product_translations.id')
            ->orderBy('product_translations.name', $orderType)
            ->select('products_view.*', 'product_translations.id as product_translation_id');
    }


}
