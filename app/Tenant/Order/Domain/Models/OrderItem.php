<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo('App\Product\Domain\Models\ProductView');
    }

    public function offer()
    {
        return $this->belongsTo('App\Offer\Domain\Models\Offer', 'offer_id', 'id');
    }


    public function freeProduct()
    {
        return $this->belongsTo('App\Product\Domain\Models\Product', 'free_product_id', 'id');
    }

    public function warranty()
    {
        return $this->belongsTo('App\Warranty\Domain\Models\Warranty', 'warranty_id', 'id');
    }
}
