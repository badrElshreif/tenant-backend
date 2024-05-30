<?php

namespace App\Infrastructure\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
	protected $fillable = [
		'target',
		'token',
		'created_at'
	];
	public $timestamps = false;
	protected $primaryKey = 'target';
}
