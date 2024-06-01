<?php

namespace App\Tenant\Offer\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class Offer extends Model
{
    use HasFactory, Translatable, Filterable;
    public $translatedAttributes = ['name'];
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function products() {
		return $this->belongsToMany('App\Product\Domain\Models\Product');
	}

	public function freeProduct()
    {
        return $this->belongsTo('App\Product\Domain\Models\Product', 'free_product_id', 'id');
    }

    public function scopeActive($query) {
        return $query->where([
                ['is_active', 1],
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')]
            ]);
    }

    public function scopeStatus($query, $is_active)
    {
        if($is_active == 1){
            return $query->where([
                ['is_active', 1],
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')]
            ]);
        }else{
            return $query->where('is_active', 0)
                ->orWhere([
                    ['is_active', 1],
                    ['start_date', '<', date('Y-m-d')],
                    ['end_date', '<', date('Y-m-d')]
                ]);
        }
    }

    public function orders()
    {
        return $this->hasMany('App\Order\Domain\Models\OrderItem', 'offer_id', 'id');
    }
}
