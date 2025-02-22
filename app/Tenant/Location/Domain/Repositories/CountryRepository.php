<?php

namespace App\Tenant\Location\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Location\Domain\Models\Country;
use Illuminate\Database\Eloquent\Builder;

class CountryRepository extends Repository
{
    private const DEFAULT_ORDER_COLUMN = 'id';
    private const DEFAULT_ORDER_TYPE = 'ASC';
    private const ACTIVE_STATUS = 1;

    /**
     * Create a new CountryRepository instance.
     *
     * @param Country $country
     */
    public function __construct(Country $country)
    {
        parent::__construct($country);
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
        return $query->join('country_translations', function ($join) {
                $join->on('countries.id', '=', 'country_translations.country_id')
                    ->where('country_translations.locale', '=', app()->getLocale());
            })
            ->orderBy('country_translations.name', $orderType)
            ->select('countries.*', 'country_translations.id as country_translation_id');
    }


}
