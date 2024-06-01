<?php

namespace App\Tenant\Brand\Domain\Models;

use App\Infrastructure\Helpers\Traits\UploaderHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class Brand extends Model
{
    use Translatable, HasFactory, Filterable, UploaderHelper;

    public $translatedAttributes = ['name', 'description'];
    protected $appends = ['min_img'];
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function products()
    {
        return $this->hasMany('App\Product\Domain\Models\Product', 'brand_id', 'id');
    }

    public function scopeActive($query, $is_active)
    {
        if ($is_active == 1) {
            return $query->where('is_active', 1);
        } else {
            return $query->where('is_active', 0);
        }
    }

    public function getMinImgAttribute()
    {
        if (isset($this->image))
            return route('image.resize', [80, 80, 'uploads', $this->image]);
        else
            return asset("assets/images/default/default-logo.png");
    }

    protected function setImageAttribute($value)
    {
        if (!empty($value)) {
            $image = explode("/", $value);
            $this->attributes['image'] = end($image);
        }
    }

    public function logo_url($w, $h)
    {
        if (isset($this->image)):
            return route('image.resize', [$w, $h, 'uploads', $this->image]);
        else:
            return asset("assets/images/default/default-logo.png");
        endif;
    }

}
