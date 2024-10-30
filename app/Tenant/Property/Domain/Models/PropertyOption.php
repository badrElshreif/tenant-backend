<?php

namespace App\Tenant\Property\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class PropertyOption extends Model
{
    use Translatable, HasFactory;

    protected $guarded = ['id'];
    public $translatedAttributes = ['name'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
