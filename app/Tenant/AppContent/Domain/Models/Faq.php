<?php

namespace App\Tenant\AppContent\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class Faq extends Model
{
    use HasFactory, Translatable, Filterable;
    protected $guarded = ['id'];
    public $translatedAttributes = ['question', 'answer'];
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
