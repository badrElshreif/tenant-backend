<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Domain\Filters\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
//use OwenIt\Auditing\Contracts\Auditable;
//class Order extends Model implements Auditable
class Payment extends Model
{
    use HasFactory, Filterable, SoftDeletes;
    //use \OwenIt\Auditing\Auditable;
    protected $guarded = ['id'];


    public function store()
    {
        return $this->belongsTo('App\Store\Domain\Models\Store');
    }

    public function orders()
    {
        return $this->hasMany('App\Tenant\Order\Domain\Models\Order');
    }

    // scope to return only requests of certain store
    public function scopeOfStore($query, $store)
    {
        return $query->where('store_id', $store);
    }

    protected function setReceiptAttribute($value)
    {
        $receipt = explode("/", $value);
        $this->attributes['receipt'] = end($receipt);
    }
    protected function getReceiptAttribute($receipt)
    {
        if (isset($receipt)):
            return \Storage::disk('public')->url('/payments/'.$receipt);
        else:
            return "";
        endif;
    }

}
