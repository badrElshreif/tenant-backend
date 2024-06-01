<?php

namespace App\Tenant\Product\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductProperty extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

	public function option() {
		return $this->belongsTo(
			'App\Property\Domain\Models\PropertyOption',
			'property_option_id'
		);
	}

	public function property() {
		return $this->belongsTo(
			'App\Property\Domain\Models\Property',
			'property_id'
		);
	}

	public function product() {
		return $this->belongsTo(
			'App\Tenant\Product\Domain\Models\Product',
			'product_id'
		);
	}

	// scope to return only not null values
    public function scopeHasPropertyValue($query)
    {
        return $query->whereNotNull('value')->orWhereHas('option');
    }
}
