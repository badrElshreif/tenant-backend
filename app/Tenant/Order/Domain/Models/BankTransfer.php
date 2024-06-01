<?php

namespace App\Tenant\Order\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo('App\Tenant\Order\Domain\Models\Order');
    }

    protected function setFileAttribute($value)
    {
        $file = explode("/", $value);
        $this->attributes['file'] = end($file);
    }
    protected function getFileAttribute($file)
    {
        if (isset($file)):
            return \Storage::disk('public')->url('/bank_transfer/'.$file);
        else:
            return "";
        endif;
    }

    public function bankAccount()
    {
        return $this->belongsTo('App\Tenant\Order\Domain\Models\BankAccount');
    }
}
