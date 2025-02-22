<?php

namespace App\Tenant\Location\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Location\Domain\Models\State;
use Illuminate\Database\Eloquent\Builder;

class StateRepository extends Repository
{
    private const DEFAULT_ORDER_COLUMN = 'id';
    private const DEFAULT_ORDER_TYPE = 'ASC';
    private const ACTIVE_STATUS = 1;

    /**
     * Create a new StateRepository instance.
     *
     * @param State $state
     */
    public function __construct(State $state)
    {
        parent::__construct($state);
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
                isset($request['country_id']),
                fn (Builder $query) => $query->where('country_id', $request['country_id'])
            )
            ->when(
                isset($request['has_active_countries']),
                fn (Builder $query) => $query->whereHas(
                    'country',
                    fn ($q) => $q->where('is_active', self::ACTIVE_STATUS)
                )
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
        return $query->join('state_translations', function ($join) {
                $join->on('states.id', '=', 'state_translations.state_id')
                    ->where('state_translations.locale', '=', app()->getLocale());
            })
            ->groupBy('states.id', 'state_translations.id')
            ->orderBy('state_translations.name', $orderType)
            ->select('states.*', 'state_translations.id as state_translation_id');
    }


}
