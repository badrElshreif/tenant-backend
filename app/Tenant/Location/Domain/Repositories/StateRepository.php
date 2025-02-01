<?php

namespace App\Tenant\Location\Domain\Repositories;


use App\Tenant\Location\Domain\Filters\StateFilter;
use App\Tenant\Location\Domain\Models\State;

class StateRepository
{
    protected $state;
    protected $filter;

    public function __construct(State $state, StateFilter $filter)
    {
        $this->state = $state;
        $this->filter = $filter;
    }

    public function query($request)
    {
        $order = $request['order_by'] ?? 'order';
        $order_type = $request['order_type'] ?? 'ASC';

        $this->state = $this->state
            ->filter($this->filter)
            ->when($order == 'name', function ($collection) use ($order_type) {
                return $collection->join('state_translations', function ($join) {
                    $join->on('states.id', '=', 'state_translations.state_id')
                        ->where('state_translations.locale', '=', app()->getLocale());
                })
                    ->groupBy('states.id')
                    ->orderBy('state_translations.name', $order_type)
                    ->select('states.*', 'state_translations.id as state_translation_id');
            })->when($order != 'name', function ($collection) use ($order, $order_type) {
                return $collection->orderBy($order, $order_type);
            });

        return $this;
    }

    public function withTrashed()
    {
        $this->state = $this->state->withTrashed();
        return $this;
    }

    public function onlyTrashed()
    {
        $this->state = $this->state->onlyTrashed();
        return $this;
    }

    public function get()
    {
        if (!$this->state) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->state->get();
    }

    /**
     * @throws \Exception
     */
    public function paginate($limit)
    {
        $limit = $limit ?? config('app.pagination_limit');
        if (!$this->state) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->state->paginate($limit);
    }

    public function getById($id)
    {
        return $this->state->find($id);
    }

    public function create(array $data)
    {
        return $this->state->create($data);
    }

    public function update($id, array $data)
    {
        $state = $this->state->find($id);
        if ($state) {
            $state->update($data);
            return $state;
        }
        return null;
    }

    public function delete($id)
    {
        $state = $this->state->find($id);
        if ($state) {
            $state->delete();
            return true;
        }
        return false;
    }
}
