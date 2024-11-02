<?php

namespace App\Tenant\Product\Domain\Models;

use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Location\Domain\Models\Country;
use App\Tenant\Offer\Domain\Models\Offer;
use App\Tenant\Order\Domain\Models\OrderItem;
use App\Tenant\Property\Domain\Models\Property;
use App\Tenant\Store\Domain\Models\Store;
use App\Uploader\Domain\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Infrastructure\Domain\Filters\Filterable;

class ProductView extends Model
{
    public $table = "products_view";
    use Translatable, HasFactory, Filterable;

    public $translatedAttributes = ['name', 'description', 'tags'];
    protected $translationForeignKey = 'product_id';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get all of the product's attachments.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function country()
    {
        return $this->belongsTo(
            Country::class,
            'made_in'
        );
    }

//    protected function setImageAttribute($value)
//    {
//        $image = explode("/", $value);
//        $this->attributes['image'] = end($image);
//    }

//    protected function getImageAttribute($image)
//    {
//        if (isset($image)):
//            return \Storage::disk('public')->url('/products/' . $image);
//        else:
//            return "";
//        endif;
//    }

    protected function setCatalogAttribute($value)
    {
        $image = explode("/", $value);
        $this->attributes['catalog'] = end($image);
    }

    protected function getCatalogAttribute($image)
    {
        if (isset($image)):
            return \Storage::disk('public')->url('/products/' . $image);
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

    protected function getPriceIncludingTaxAttribute($value)
    {
        if ($this->category)
            return $value + ($value * $this->category->tax_percentage / 100);
        return $value;
    }

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
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'product_property', 'product_id', 'property_id')->withPivot('property_option_id', 'value')->withTimestamps();
    }


    protected function getIsActiveAttribute($value)
    {
        if (!isset($this->deactivation_start_date) && !isset($this->deactivation_end_date))
            return (bool)$value;
        if ($value == 1 && $this->deactivation_start_date <= date('Y-m-d') && $this->deactivation_end_date >= date('Y-m-d'))
            return (bool)!$value;
        return (bool)$value;
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'product_id', 'id');
    }

    public function scopeActive($query, $is_active)
    {
        if ($is_active == 0) {
            return $query->where('is_active', '<>', 1)
                ->orWhere([
                    ['is_active', 1],
                    ['deactivation_start_date', '<=', date('Y-m-d')],
                    ['deactivation_end_date', '>=', date('Y-m-d')]
                ]);
        } else {
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

    public function offer()
    {
        return $this->belongsToMany(Offer::class, 'offer_product', 'product_id', 'offer_id')
            ->where([
                ['is_active', 1],
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')]
            ]);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'product_id', 'id');
    }

    public function imageUrl($w, $h)
    {
        if (isset($this->image)):
            return routeTenant('tenant.image.resize',
                [$w, $h, 'products', $this->image],
            );
        else:
            return asset("assets/images/default/default-logo.png");
        endif;
    }


}

