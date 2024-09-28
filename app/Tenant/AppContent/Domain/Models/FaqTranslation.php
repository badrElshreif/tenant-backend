<?php

namespace App\Tenant\AppContent\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['question', 'answer', 'locale', 'faq_id'];
}
