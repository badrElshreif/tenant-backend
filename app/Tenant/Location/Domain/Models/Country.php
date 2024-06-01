<?php

namespace App\Tenant\Location\Domain\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class Country extends Model
{
    use Translatable, SoftDeletes, HasFactory, Filterable;

    public $translatedAttributes = ['name'];

    protected $guarded = [
        "id",
    ];
    protected $casts = [
        'is_active' => 'boolean'
    ];


    protected function setFlagAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['flag'] = end($image);
    }
    protected function getFlagAttribute($image)
    {
        if (isset($image)):
            return \Storage::disk('public')->url('/countries/'.$image);
        else:
            return "";
        endif;
    }

    public function scopeActive($query, $is_active)
    {
        if($is_active == 1){
            return $query->where('is_active', 1);
        }else{
            return $query->where('is_active', 0);
        }
    }

    public function states()
    {
        return $this->hasMany('App\Tenant\Location\Domain\Models\State');
    }

    public function addresses()
    {
        return $this->hasMany('App\User\Domain\Models\UserAddress');
    }
}
