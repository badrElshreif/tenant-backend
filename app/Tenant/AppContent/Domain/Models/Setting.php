<?php

namespace App\Tenant\AppContent\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Str;

class Setting extends Model
{
    use HasFactory, Translatable;
    protected $guarded = ['id'];
    public $translatedAttributes = ['name'];

    protected function setBodyAttribute($value)
    {
        // if (preg_match('https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()!@:%_\+.~#?&\/\/=]*)', $value) === 1) {
    	if(Str::endsWith($value, ['.png', 'jpg', 'jpeg'])){
            $value = explode("/", $value);
        	$this->attributes['body'] = end($value);
        // }elseif($this->key == 'variable_pricing_categories'){
        //     $this->attributes['body'] = json_encode($value);
        }else{
            $this->attributes['body'] = $value;
        }
    }

    protected function getBodyAttribute($value)
    {
        if (isset($value) && Str::endsWith($value, ['.png', '.jpg', '.jpeg'])):
            if(tenant() != null)
                return \Storage::disk('public')->url('tenant_' . tenant()->id .'/settings/'.$value);
            return \Storage::disk('public')->url('/settings/'.$value);
        elseif($this->key == 'can_rate' || $this->key == 'is_refund_connected_with_waseet' || $this->key == 'show_mobile_popup_message'):
            return (bool)$value;
        elseif($this->key == 'variable_pricing_categories'):
            return  array_map('intval', explode(',', $value));
            //return json_decode($value);
        else:
            return $value;
        endif;
    }

    public function propertyType() {
        return $this->belongsTo('App\Property\Domain\Models\PropertyType');
    }
}
