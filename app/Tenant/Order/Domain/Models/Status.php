<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Status extends Model
{
    use Translatable, HasFactory;
    public $translatedAttributes = ['name'];
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
