<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Domain\Filters\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

//use OwenIt\Auditing\Contracts\Auditable;
//class Order extends Model implements Auditable
class Order extends Model
{
    use HasFactory, Filterable, SoftDeletes;
    //use \OwenIt\Auditing\Auditable;
    protected $guarded = ['id'];
    protected $casts = [
        'is_gift' => 'boolean',
        'is_paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\User\Domain\Models\User');
    }

    public function orderItems()
    {
        return $this->hasMany('App\Tenant\Order\Domain\Models\OrderItem');
    }

    public function promoCode()
    {
        return $this->belongsTo('App\PromoCode\Domain\Models\PromoCode');
    }

    public function status()
    {
        return $this->belongsTo('App\Tenant\Order\Domain\Models\Status');
    }

    public function store()
    {
        return $this->belongsTo('App\Store\Domain\Models\Store');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Tenant\Order\Domain\Models\PaymentMethod');
    }

    public function shippingCompany()
    {
        return $this->belongsTo('App\Tenant\Order\Domain\Models\ShippingCompany');
    }

    public function userAddress()
    {
        return $this->belongsTo('App\User\Domain\Models\UserAddress');
    }

    public function orderService()
    {
        return $this->hasOne('App\Tenant\Order\Domain\Models\OrderService');
    }

    public function bankTransfer()
    {
        return $this->hasOne('App\Tenant\Order\Domain\Models\BankTransfer');
    }

    public function refund()
    {
        return $this->hasOne('App\Refund\Domain\Models\Refund');
    }

    public function statuses()
    {
        return $this->hasMany('App\Tenant\Order\Domain\Models\OrderStatus')->where('type', 'order');
    }

    // scope to return only requests of certain store
    public function scopeOfStore($query, $store)
    {
        return $query->where('store_id', $store);
    }

    protected function getQrCodeAttribute($image)
    {
        if (isset($image)):
            return \Storage::disk('public')->url('/barcodes/'.$image);
        else:
            return "";
        endif;
    }

    protected function getInvoiceFileAttribute($image)
    {
        if (isset($image)):
            return \Storage::disk('public')->url('/invoices/'.$image);
        else:
            return "";
        endif;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('is_paid', function (Builder $builder) {
            $builder->where('is_paid','=',1);
        });
    }
}
