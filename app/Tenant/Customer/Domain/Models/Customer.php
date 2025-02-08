<?php

namespace App\Tenant\Customer\Domain\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Infrastructure\Domain\Filters\Filterable;

class Customer extends Authenticatable
//class Customer extends Authenticatable
{
    protected $guard_name = 'api';
    //use HasApiTokens;
    use HasFactory;

    //use HasProfilePhoto;
    use Notifiable, Filterable;

    //use TwoFactorAuthenticatable;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        "id"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'country_code' => $this->country_code,
        ];
    }

    protected function setAvatarAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['avatar'] = end($image);
    }

    protected function getAvatarAttribute($image)
    {
        if (isset($image) && $image != "")
            return \Storage::disk('public')->url('/users/avatar/' . $image);

        return url("assets/images/default/default.jpg");
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function ratings()
    {
        return $this->hasMany('App\Product\Domain\Models\Rating');
    }

    public function statuses()
    {
        return $this->hasMany('App\User\Domain\Models\UserStatus');
    }

    public function favourites()
    {
        return $this->belongsToMany(
            'App\Product\Domain\Models\ProductView',
            'favourites',
            'user_id',
            'product_id'
        );
    }

    public function orders()
    {
        return $this->hasMany('App\Order\Domain\Models\Order');
    }

    public function tokens()
    {
        return $this->morphMany('App\User\Domain\Models\DeviceToken', 'tokenable');
    }

    public function orderStatuses()
    {
        return $this->morphMany('App\Order\Domain\Models\OrderStatus', 'statusable');
    }

    public function notifications()
    {
        return $this->morphMany('App\Notification\Domain\Models\Notification', 'notifiable');
    }

    public function transactions()
    {
        return $this->hasMany('App\Order\Domain\Models\Transaction');
    }

    public function wallet()
    {
        return optional($this->transactions()->orderBy('id', 'desc')->first())->wallet_total;
    }

    public function cart()
    {
        return $this->hasMany('App\Order\Domain\Models\Cart');
    }

}
