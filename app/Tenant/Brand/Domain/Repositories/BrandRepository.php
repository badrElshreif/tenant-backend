<?php

namespace App\Tenant\Brand\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Brand\Domain\Models\Brand;
use Illuminate\Database\Eloquent\Builder;

class BrandRepository extends Repository
{
    private const DEFAULT_ORDER_COLUMN = 'id';
    private const DEFAULT_ORDER_TYPE = 'ASC';
    private const ACTIVE_STATUS = 1;

    /**
     * Create a new BrandRepository instance.
     *
     * @param Brand $brand
     */
    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
    }

    /**
     * Build query with filters and sorting
     *
     * @param array $request
     * @return self
     */
    public function filter($request): self
    {
        $orderColumn = $request['order_by'] ?? self::DEFAULT_ORDER_COLUMN;
        $orderType = $request['order_type'] ?? self::DEFAULT_ORDER_TYPE;

        $this->model = $this->model
            ->when(
                isset($request['active']),
                fn (Builder $query) => $query->where('brands.is_active', (bool)$request['active'])
            )
            ->when(
                isset($request['search']),
                fn (Builder $query) => $this->searchByName($query, $request['search'])
            )
            ->when(
                $orderColumn === 'name',
                fn (Builder $query) => $this->orderByTranslatedName($query, $orderType),
                fn (Builder $query) => $query->orderBy("brands.".$orderColumn, $orderType)
            );

        return $this;
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
        return $query->join('brand_translations', function ($join) {
                $join->on('brands.id', '=', 'brand_translations.brand_id')
                    ->where('brand_translations.locale', '=', app()->getLocale());
            })
            ->groupBy('brands.id', 'brand_translations.id')
            ->orderBy('brand_translations.name', $orderType)
            ->select('brands.*', 'brand_translations.id as brand_translation_id');
    }

    private function searchByName(Builder $query, string $search): Builder
    {
        return $query->join('brand_translations', function ($join) use ($search) {
            $join->on('brands.id', '=', 'brand_translations.brand_id')
                ->where('brand_translations.locale', '=', app()->getLocale())
                ->where('brand_translations.name', 'like', "%{$search}%");
        });
    }

}
