<?php

namespace App\Tenant\Order\Domain\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlinePaymentMethod extends Model
{
    use Translatable, HasFactory;

    public $translatedAttributes = ['name'];
    public $translationForeignKey = 'payment_id';
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('status',1);
    }

    protected function getImageAttribute($image)
    {
        if (isset($image)):
            return url('assets/images/'.$image);
        else:
            return "";
        endif;
    }

}
