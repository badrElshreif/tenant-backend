<?php

namespace App\Tenant\Category\Domain\Models;

use App\Tenant\Product\Domain\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class Category extends Model
{
    use Translatable, HasFactory, Filterable;

    public $translatedAttributes = ['name', 'description'];
    protected $fillable = ['order', 'parent_id', 'image', 'is_active', 'tax_percentage', 'type'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function childs()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')
            ->withCount('products')->with('childs');
    }

    public function properties()
    {
        return $this->belongsToMany('App\Property\Domain\Models\Property')
            ->where('is_active', 1);
    }

    protected function setImageAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['image'] = end($image);
    }

    protected function getImageAttribute($image)
    {
        if (isset($image)):
            return \Storage::disk('public')->url('/categories/' . $image);
        else:
            return "";
        endif;
    }

    public function scopeActive($query, $is_active)
    {
        if ($is_active == 1) {
            return $query->where('is_active', 1);
        } else {
            return $query->where('is_active', 0);
        }
    }

    public function scopeType($query, $type = 'store_products')
    {
        return $query->where('type', $type);
    }

    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }
}
