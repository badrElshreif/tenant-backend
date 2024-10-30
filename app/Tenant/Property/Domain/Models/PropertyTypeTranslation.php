<?php

namespace App\Tenant\Property\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyTypeTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];
}
