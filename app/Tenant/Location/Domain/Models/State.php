<?php

namespace App\Tenant\Location\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Domain\Filters\Filterable;

class State extends Model
{
    use Translatable, HasFactory, SoftDeletes, Filterable;
    public $translatedAttributes = ['name'];
    protected $fillable = ['country_id', 'is_active'];
    // protected $guarded = [
    //     "id",
    // ];

    protected $table = 'states';
    protected $casts = [
        'is_active' => 'boolean'
    ];


    public function country()
    {
        return $this->belongsTo('App\Tenant\Location\Domain\Models\Country');
    }

    public function scopeActive($query, $is_active)
    {
        if($is_active == 1){
            return $query->where('is_active', 1);
        }else{
            return $query->where('is_active', 0);
        }
    }

    public function cities()
    {
        return $this->hasMany('App\Tenant\Location\Domain\Models\City');
    }

    public function addresses()
    {
        return $this->hasMany('App\User\Domain\Models\UserAddress');
    }
}
