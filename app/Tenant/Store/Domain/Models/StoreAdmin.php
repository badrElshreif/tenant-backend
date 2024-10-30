<?php

namespace App\Tenant\Store\Domain\Models;


use App\Tenant\Admin\Domain\Models\Admin;

class StoreAdmin extends Admin
{
    protected $table = 'admins';
    protected $guard_name = 'store';

    public function store()
    {
        return $this->hasOne(Store::class, 'seller_id');
    }
}
