<?php
namespace App\Infrastructure\Domain\Repositories;

use App\Infrastructure\Domain\Contracts\RepositoryInterface;
use Illuminate\Support\Str;

abstract class Repository implements RepositoryInterface
{
    protected $connector = 'and', $betweenQuery = null, $parameters;

    public function __call($method, $arguments)
    {
        return $this->model->{$method}(...$arguments);
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->getById($id);
        $record->update($attributes);
        return $record;
    }

    public function delete($id)
    {
        return $this->getById($id)->delete();
    }
}
