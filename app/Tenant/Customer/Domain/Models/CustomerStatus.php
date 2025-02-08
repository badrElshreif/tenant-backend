<?php

namespace App\Tenant\Customer\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStatus extends Model
{
    use HasFactory;
    protected $guarded =[
		'id'
	];

	public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
