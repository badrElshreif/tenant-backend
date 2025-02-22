<?php

namespace App\Tenant\Category\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Category\Domain\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoryRepository extends Repository
{
    private const DEFAULT_ORDER_COLUMN = 'id';
    private const DEFAULT_ORDER_TYPE = 'ASC';
    private const ACTIVE_STATUS = 1;

    /**
     * Create a new BrandRepository instance.
     *
     * @param Brand $brand
     */
    public function __construct(Category $category)
    {
        parent::__construct($category);
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
                fn (Builder $query) => $query->where('is_active', (bool)$request['active'])
            )
            ->when(
                $orderColumn === 'name',
                fn (Builder $query) => $this->orderByTranslatedName($query, $orderType),
                fn (Builder $query) => $query->orderBy($orderColumn, $orderType)
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
        return $query->join('category_translations', function ($join) {
                $join->on('categories.id', '=', 'category_translations.category_id')
                    ->where('category_translations.locale', '=', app()->getLocale());
            })
            ->groupBy('categories.id', 'category_translations.id')
            ->orderBy('category_translations.name', $orderType)
            ->select('categories.*', 'category_translations.id as category_translation_id');
    }

}
