<?php

namespace App\Tenant\Location\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Location\Domain\Models\City;
use Illuminate\Database\Eloquent\Builder;

class CityRepository extends Repository
{
    private const DEFAULT_ORDER_COLUMN = 'id';
    private const DEFAULT_ORDER_TYPE = 'ASC';
    private const ACTIVE_STATUS = 1;

    public function __construct(City $city)
    {
        parent::__construct($city);
    }

    /**
     * Build query with filters and sorting
     *
     * @param array $request
     * @return self
     */
    public function query(array $request): self
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
            )
            ->when(
                isset($request['state_id']),
                fn (Builder $query) => $query->where('state_id', $request['state_id'])
            )
            ->when(
                isset($request['has_active_countries']),
                fn (Builder $query) => $query->whereHas(
                    'country',
                    fn ($q) => $q->where('is_active', self::ACTIVE_STATUS)
                )
            )
            ->when(
                isset($request['has_active_states']),
                fn (Builder $query) => $query->whereHas(
                    'state',
                    fn ($q) => $q->where('is_active', self::ACTIVE_STATUS)
                )
            );

        return $this;
    }

    /**
     * Order cities by translated name
     *
     * @param Builder $query
     * @param string $orderType
     * @return Builder
     */
    private function orderByTranslatedName(Builder $query, string $orderType): Builder
    {
        return $query->join('city_translations', function ($join) {
                $join->on('cities.id', '=', 'city_translations.city_id')
                    ->where('city_translations.locale', '=', app()->getLocale());
            })
            ->groupBy('cities.id', 'city_translations.id')
            ->orderBy('city_translations.name', $orderType)
            ->select('cities.*', 'city_translations.id as city_translation_id');
    }
    
}