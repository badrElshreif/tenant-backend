<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlinePaymentMethodTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['name','payment_id'];

    public function onlinePaymentMethod()
    {
        return $this->belongsTo(OnlinePaymentMethod::class,'payment_id');
    }
}
