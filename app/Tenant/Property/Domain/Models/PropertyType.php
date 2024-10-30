<?php

namespace App\Tenant\Property\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class PropertyType extends Model
{
    use Translatable, HasFactory, Filterable;

    public $translatedAttributes = ['name'];
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'has_options' => 'boolean'
    ];
}
