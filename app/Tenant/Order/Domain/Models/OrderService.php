<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function service()
    {
        return $this->belongsTo('App\Product\Domain\Models\Product', 'service_id', 'id');
    }

    public function subcategory()
    {
        return $this->belongsTo('App\Category\Domain\Models\Category', 'subcategory_id', 'id');
    }
}
