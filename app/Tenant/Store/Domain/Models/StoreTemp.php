<?php

namespace App\Tenant\Store\Domain\Models;

use App\Infrastructure\Domain\Filters\Filterable;
use App\Location\Domain\Models\Country;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTemp extends Model
{
    use Translatable, HasFactory, Filterable;
    public $translatedAttributes = ['name'];
    public $translationForeignKey = 'store_temp_id';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'has_delivery_service' => 'boolean'
    ];

    //status = [0 => 'new', 1 => 'accepted store', 2 => 'rejected store']
    //is_active = [0 => 'in active store and accepted', 1 => 'active store and accepted']

    public function city()
    {
        return $this->belongsTo('App\Location\Domain\Models\City');
    }
    public function bankCountry()
    {
        return $this->belongsTo(Country::class,'bank_country_id');
    }
    protected function setImageAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['image'] = end($image);
    }

    protected function getImageAttribute($image)
    {
        if (isset($image) && $image != ""):
            return \Storage::disk('public')->url('/stores/' . $image);
        else:
            //return "";
            if ($this->type == 'centers')
                return url("assets/images/default/center.png");
            else
                return url("assets/images/default/store.png");
        endif;
    }


    protected function setCommercialRegistryPhotoAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['commercial_registry_photo'] = end($image);
    }

    protected function getCommercialRegistryPhotoAttribute($image)
    {
        if (isset($image) && $image != ""):
            return \Storage::disk('public')->url('/stores/' . $image);
        else:
            return "";
        endif;
    }

    public function scopeActive($query, $is_active)
    {
        if ($is_active == 1) {
            return $query->where('status', 1)->where('is_active', 1);
        } else {
            return $query->where('is_active', 0);
        }
    }

    public function scopeNearest($query, $latitude, $longitude)
    {
        return $query->select(DB::raw('*, ( 6371 * acos( cos( radians(' .
            $latitude . ') ) * cos( radians( `latitude` ) ) * cos(radians( `longitude` ) - radians(' .
            $longitude . ') ) + sin( radians(' . $latitude .
            ') ) * sin( radians( `latitude` ) ) ) ) as distance'))
//            ->having('distance', '<=', 50)
            ->orderBy('distance');
    }

    public function admins()
    {
        return $this->hasMany('App\Admin\Domain\Models\Admin');
    }

    public function products()
    {
        return $this->hasMany('App\Product\Domain\Models\Product');
    }

    public function promoCodes()
    {
        return $this->belongsToMany('App\PromoCode\Domain\Models\PromoCode');
    }

}
