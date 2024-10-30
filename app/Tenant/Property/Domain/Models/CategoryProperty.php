<?php

namespace App\Product\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProperty extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
}
