<?php

namespace App\Tenant\Admin\Domain\Models;

use App\Tenant\Order\Domain\Models\OrderStatus;
use App\Tenant\Store\Domain\Models\Store;
use App\Uploader\Domain\Models\Attachment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Infrastructure\Domain\Filters\Filterable;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    // use HasFactory;
    //use HasProfilePhoto;
    use Notifiable, HasRoles, Filterable, HasApiTokens;

    //protected $guard_name = 'admin';
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        "id",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $connection = "tenant";
    protected $casts = [
        'email_verified_at' => 'datetime',
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

//    protected static function booted()
//    {
//        static::addGlobalScope('is_seller', function (Builder $builder){
//           $builder->where('is_seller', false);
//        });
//    }

    protected function setAvatarAttribute($value)
    {
        $image = explode("/", $value);
        if (!in_array('default-user.jpg', $image))
            $this->attributes['avatar'] = end($image);
        else
            $this->attributes['avatar'] = null;
    }

    protected function getAvatarAttribute($image)
    {
        if (isset($image) && $image != ""):
            return \Storage::disk('public')->url('/admins/' . $image);
        else:
            return url("assets/images/default/default-user.jpg");
        endif;
    }

    public function allRoles()
    {
        // return $this->belongsToMany(
        //     'App\Tenant\Admin\Domain\Models\Role',
        //     'model_has_roles',
        //     'model_id',
        //     'role_id'
        // );

        return $this->morphToMany(
            'App\Tenant\Admin\Domain\Models\Role',
            'model_has_roles',
            'model_id',
            'role_id'
        );
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'creatable');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orderStatuses()
    {
        return $this->morphMany(OrderStatus::class, 'statusable');
    }

    public function tokens()
    {
        return $this->morphMany('App\User\Domain\Models\DeviceToken', 'tokenable');
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'seller_id');
    }


    public function findForPassport($username) {
        return self::where('email', $username)->first(); // change column name whatever you use in credentials
    }
}
