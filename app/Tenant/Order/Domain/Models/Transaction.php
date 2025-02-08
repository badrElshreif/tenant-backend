<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
class Transaction extends Model
{
    use HasFactory, Translatable;
    protected $guarded = ['id'];

    public $translatedAttributes = ['reason'];

    public function user()
    {
        return $this->belongsTo('App\User\Domain\Models\Customer');
    }

    /**
     * Get the parent transactable model (order or refund).
     */
    public function transactable()
    {
        return $this->morphTo();
    }
}
