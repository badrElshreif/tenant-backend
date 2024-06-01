<?php

namespace App\Tenant\Product\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    use HasFactory;
    protected $table = 'product_translations';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'tags' => 'array'
    ];

    protected function getTagsAttribute($value)
    {
        if (isset($value)):
            return (array) json_decode($value);
        else:
            return [];
        endif;
    }
}
