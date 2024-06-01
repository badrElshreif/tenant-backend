<?php

namespace App\Tenant\Offer\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['name', 'locale', 'offer_id'];
}
