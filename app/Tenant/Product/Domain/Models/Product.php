<?php

namespace App\Tenant\Product\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;
//use OwenIt\Auditing\Contracts\Auditable;

//class Product extends Model implements Auditable
class Product extends Model
{
    use Translatable, HasFactory, Filterable;
    //use \OwenIt\Auditing\Auditable;
    public $translatedAttributes = ['name', 'description', 'tags'];
    protected $guarded = ['id'];
    public static function factory(...$parameters)
    {

    }

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get all of the product's attachments.
     */
    public function attachments()
    {
        return $this->morphMany('App\Uploader\Domain\Models\Attachment', 'attachable');
    }

    public function country() {
        return $this->belongsTo(
            'App\Location\Domain\Models\Country',
            'made_in'
        );
    }

    protected function setImageAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['image'] = end($image);
    }
    protected function getImageAttribute($image)
    {
        if (isset($image)):
            return \Storage::disk('public')->url('/products/'.$image);
        else:
            return "";
        endif;
    }

    protected function setCatalogAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['catalog'] = end($image);
    }
    protected function getCatalogAttribute($image)
    {
        if (isset($image)):
            return \Storage::disk('public')->url('/products/'.$image);
        else:
            return "";
        endif;
    }

    // public function getImageAttribute($image)
    // {
    //     return [
    //         'name' => $image,
    //         'folder' => 'products',
    //         'file' =>\Storage::disk('public')->url('/products/'.$image),
    //         'type' => 'image'
    //     ];
    // }

    protected function getPriceIncludingTaxAttribute(){
        return $this->price + ($this->price * setting('added_tax') / 100);
    }

    protected function getDiscountAttribute(){
        $discount = 0.00;
        $free_product = null;
        $offer = $this->offer->first();
        if ($offer){
            if ($offer->type == 'percentage'){
                $discount = $this->price_including_tax * $offer->value / 100;
            }else if ($offer->type == 'value'){
                $discount = $offer->value;
            } else if ($offer->type == "free_product"){
                $free_product = $offer->free_product_id;
            }else {
                $discount = 0.00;
            }
        }
        //return $discount;
        return array ('offer_id' => optional($offer)->id, 'offer_discount' => $discount, 'free_product' => $free_product);

    }

    // scope to return only requests of certain school
    public function scopeOfStore($query, $store)
    {
        return $query->where('store_id', $store);
    }

    // public function properties()
    // {
    //     return $this->hasMany('App\Tenant\Product\Domain\Models\ProductProperty');
    // }

    public function orders()
    {
        return $this->hasMany('App\Order\Domain\Models\OrderItem');
    }

    public function serviceOrders()
    {
        return $this->hasMany('App\Order\Domain\Models\OrderService', 'service_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category\Domain\Models\Category');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand\Domain\Models\Brand');
    }

    public function store()
    {
        return $this->belongsTo('App\Store\Domain\Models\Store');
    }

    /**
     * The certificates that belong to the student.
     */
    public function properties()
    {
        return $this->belongsToMany('App\Property\Domain\Models\Property')->withPivot('property_option_id', 'value')->withTimestamps();
    }


    protected function getIsActiveAttribute($value){
        if (!isset($this->deactivation_start_date) && !isset($this->deactivation_end_date))
            return (bool) $value;
        if($value == 1 && $this->deactivation_start_date <= date('Y-m-d') && $this->deactivation_end_date >= date('Y-m-d'))
            return (bool) !$value;
        return (bool) $value;
    }

    public function offers() {
        return $this->belongsToMany('App\Offer\Domain\Models\Offer');
    }

    public function scopeActive($query, $is_active)
    {
        if($is_active == 0){
            return $query->where('is_active','<>', 1)
                ->orWhere([
                    ['is_active', 1],
                    ['deactivation_start_date', '<=', date('Y-m-d')],
                    ['deactivation_end_date', '>=', date('Y-m-d')]
                ]);
        }else{
            return $query->where([
                    ['is_active', 1],
                    ['deactivation_start_date', null],
                    ['deactivation_end_date', null]
                ])
                ->orWhere([
                    ['is_active', 1],
                    ['deactivation_start_date', '>', date('Y-m-d')],
                    ['deactivation_end_date', '<', date('Y-m-d')]
                ]);
        }
    }

    public function offer() {
        return $this->belongsToMany('App\Offer\Domain\Models\Offer')
            ->where([
                ['is_active', 1],
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')]
            ]);
    }

    public function ratings()
    {
        return $this->hasMany('App\Tenant\Product\Domain\Models\Rating');
    }


}

