<?php

namespace App\Infrastructure\Domain\Contracts;
interface RepositoryInterface
{
    public function find($id);
    public function findOrFail($id);

    public function findBy(string $column, $value);
    public function findByOrFail(string $column, $value);

    public function withTrashed();

    public function onlyTrashed();

    public function filter($request);

    public function get();

    public function paginate($limit);

    public function create(array $attributes);

    public function update($id, array $attributes);
    public function updateOrFail($id, array $attributes);

    public function delete($id);
}
