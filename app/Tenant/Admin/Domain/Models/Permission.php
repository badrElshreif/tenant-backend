<?php

namespace App\Tenant\Admin\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use Translatable, HasFactory, Filterable;
    public $translatedAttributes = ['display_name', 'key_name'];
    protected $fillable = ['name', 'is_active', 'guard_name'];

}
