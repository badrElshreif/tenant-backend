<?php

namespace App\Infrastructure\Traits;

use App\Main\Tenant\Domain\Models\Tenant;

trait TenantSetConnection
{

 //   protected $connection = 'tenant';
    public function getConnectionName()
    {
        $name = "tenant";
        if (config('set_store_connection')) {
            $name = "tenant";
        }
        return $name;
    }


}
