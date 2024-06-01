<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
	protected $guarded =[
		'id'
	];
	protected $table = 'order_statuses';

	/**
     * Get the parent statusable model (user or admin).
     */
    public function statusable()
    {
        return $this->morphTo();
    }

    public function status()
    {
        return $this->belongsTo('App\Tenant\Order\Domain\Models\Status');
    }

    public function order()
    {
        return $this->belongsTo('App\Tenant\Order\Domain\Models\Order');
    }
}
