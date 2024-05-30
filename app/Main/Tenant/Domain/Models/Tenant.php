<?php

namespace App\Main\Tenant\Domain\Models;

use App\Infrastructure\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends MainModel
{
    protected $table = "tenants";
    protected $guarded = [];

}
