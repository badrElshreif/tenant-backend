<?php

namespace App\Tenant\Property\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];
}
