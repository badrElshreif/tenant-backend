<?php
namespace App\User\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\User\Domain\Models\Customer;

class UserRepository extends Repository
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }
}
