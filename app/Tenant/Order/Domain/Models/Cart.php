<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User\Domain\Models\User');
    }

    public function product()
    {
        return $this->belongsTo('App\Product\Domain\Models\ProductView');
    }

    public function store()
    {
        return $this->belongsTo('App\Store\Domain\Models\Store');
    }

    public function warranty()
    {
        return $this->belongsTo('App\Warranty\Domain\Models\Warranty');
    }

}
