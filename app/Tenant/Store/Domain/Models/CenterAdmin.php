<?php
namespace App\Tenant\Store\Domain\Models;

use App\Admin\Domain\Models\Admin;

class CenterAdmin extends Admin {
	protected $table = 'admins';
   protected $guard_name = 'center';
}
