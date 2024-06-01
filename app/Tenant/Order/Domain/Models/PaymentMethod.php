<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class PaymentMethod extends Model
{
    use Translatable, HasFactory, Filterable;
    public $translatedAttributes = ['name'];
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
