<?php

namespace App\Tenant\Customer\Domain\Models;

use App\Tenant\Location\Domain\Models\City;
use App\Tenant\Location\Domain\Models\Country;
use App\Tenant\Location\Domain\Models\State;
use App\Tenant\Order\Domain\Models\Order;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $guarded = [
        'id'
    ];
    protected $table = 'user_addresses';
    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(Customer::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

