<?php

namespace App\Tenant\Location\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

}
