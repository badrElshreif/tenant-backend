<?php

namespace App\Tenant\Customer\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
	protected $guarded =[
		'id'
	];
	protected $table = 'device_tokens';

	/**
     * Get the parent tokenable model (user or admin).
     */
    public function tokenable()
    {
        return $this->morphTo();
    }
}
