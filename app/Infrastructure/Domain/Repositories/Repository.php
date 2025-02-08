<?php

namespace App\Infrastructure\Domain\Repositories;

use App\Infrastructure\Domain\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }


    public function findBy($column, $value)
    {
        return $this->model->where($column, $value)->first();
    }

    public function findByOrFail($column, $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function withTrashed()
    {
        $this->model = $this->model->withTrashed();
        return $this;
    }

    public function onlyTrashed()
    {
        $this->model = $this->model->onlyTrashed();
        return $this;
    }

    public function filter($request)
    {
        return $this;
    }

    public function get()
    {
        if (!$this->model) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->model->get();
    }

    public function paginate($limit)
    {
        $limit = $limit ?? config('app.pagination_limit');
        if (!$this->model) {
            throw new \Exception('Query not initialized. Use query() method first.');
        }
        return $this->model->paginate($limit);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }
}
