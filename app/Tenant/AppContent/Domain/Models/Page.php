<?php

namespace App\Tenant\AppContent\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Page extends Model
{
    use HasFactory, Translatable;
    protected $guarded = ['id', 'slug'];
    public $translatedAttributes = ['title', 'body'];
}
