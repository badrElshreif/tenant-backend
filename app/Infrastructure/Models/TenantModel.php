<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class TenantModel extends Model
{
    protected $connection = 'tenant';
}
