<?php

namespace App\Tenant\Property\Domain\Models;

use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Product\Domain\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class Property extends Model
{
    use Translatable, HasFactory, Filterable;

    public $translatedAttributes = ['name'];
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function propertyOptions()
    {
        return $this->hasMany(PropertyOption::class);
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_property',
            'property_id',
            'product_id'
        );
    }

}
