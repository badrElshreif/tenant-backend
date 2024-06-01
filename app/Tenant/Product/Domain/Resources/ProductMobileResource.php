<?php

namespace App\Tenant\Product\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tenant\Product\Domain\Resources\PropertyOptionResource;
use App\Tenant\Product\Domain\Resources\CategoryMobileResource;
use App\Tenant\Product\Domain\Resources\BrandMobileResource;
use App\Tenant\Product\Domain\Resources\ProductExtraPropertyLiteResource;
use App\Tenant\Product\Domain\Resources\ProductAttachmentResource;
use App\Tenant\Product\Domain\Models\Product;
use App\Tenant\Product\Domain\Models\ProductExtraProperty;
class ProductMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $discount = 0.00;
        $free_product = null;
        $offer = null;
        $offer = $this->offer->first();

        if ($offer){
            if ($offer->type == "free_product"){
                $free_product = new ProductMobileLiteResource($offer->freeProduct);
            }
            else if ($offer->type == 'percentage'){

                $discount = ($this->price_including_tax * $offer->value) / 100;
            }
            else{
                $discount = $offer->value;
            }
        }

        $similar_products = null;
        $reviews = [];
        $review_statistics = [];
        $has_property_options = false;
        if(request()->isMethod('get') && isset(request()->id)){
            $similar_products =  Product::active(1)->whereCategoryId($this->category_id)->where('id', '!=', $this->id)->orderBy('id', 'desc')->limit(10)->get();
            //$reviews = RatingResource::collection($this->ratings);
            $review_statistics = [];
            for ($i=5; $i > 0; $i--) {
                $rate = array(
                    'rate' => $i,
                    'users_no' => $this->ratings->where('rate', '>', $i-1)->where('rate', '<=', $i)->where('is_active', 1)->count('user_id')
                );
                array_push($review_statistics, $rate);
            }

            $property_has_options_check = ProductExtraProperty::whereProductId($this->id)->whereHas('property', function($property){
                    $property->where('has_options', 1);
            })->first();
            if($property_has_options_check)
                $has_property_options = true;
        }

        $is_favourite = false;
        if(auth()->check()){
            if(auth()->user()->favourites){
                if(in_array($this->id, auth()->user()->favourites->pluck('id')->toArray()))
                $is_favourite = true;
            }

        }

        $is_rated = false;
        if(auth()->check()){
            if(auth()->user()->ratings){
                if(auth()->user()->ratings->where('product_id', $this->id)->first())
                    $is_rated = true;
            }
        }

        $product_rosource =  [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'tags' => $this->tags,
            'primary_attachment' => $this->primaryAttachment->count() > 0 ? new ProductAttachmentResource($this->primaryAttachment[0]) : null,
            'attachments' => ProductAttachmentResource::collection($this->attachments),
            'catalog' => $this->catalog,
            // 'category' => optional(optional($this->category->parent)->translate(app()->getLocale()))->name,
            // 'subcategory' => optional($this->category->translate(app()->getLocale()))->name,
            // 'brand' => optional($this->brand->translate(app()->getLocale()))->name,
            'category' => new GenericNameResource($this->category->parent),
            'subcategory' => new GenericNameResource($this->category),
            'brand' => new BrandMobileResource($this->brand),
            'quantity' => $this->quantity > $max_available_product_quantity ? $max_available_product_quantity : $this->quantity,
            'price' => number_format($this->price, 2, '.', ''),
            'price_after_tax' => number_format($this->price_including_tax, 2, '.', ''),
            //'tax' => $tax,
            'discount' => $discount > 0 ?number_format((float)($discount/$this->price_including_tax)*100, 2, '.', '') : 0.00,
            //'discount' => $discount,
            //'price_after_tax' => number_format((float)$this->price + $tax, 2, '.', ''),
            'price_after_discount' => number_format((float)$this->price_including_tax - $discount , 2, '.', ''),
            'free_product' => $free_product,
            'barcode' => $this->barcode,
            'max_purchase_quantity' => $this->max_purchase_quantity,
            // 'extra_properties' => ProductExtraPropertyLiteResource::collection($this->properties()->hasPropertyValue()->get()),
            'extra_properties' => ProductExtraPropertyLiteResource::collection($this->properties),
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            // 'rate' => count($this->ratings) > 0 ? number_format((float)$this->ratings->avg('rate'), 2, '.', '') : 0,
            'rate' => round($this->ratings->where('is_active', 1)->avg('rate'), 2),

            'rating_users_no' => $this->ratings->count('user_id'),
            'rating_statistics' => $review_statistics,
            'is_favourite' => $is_favourite,
            'is_rated' => $is_rated,
            'sales_count' => $this->orders->count('product_id'),
            'reviews' => $reviews,
            'similar_products' => $similar_products ? ProductLiteResource::collection($similar_products) : []
        ];
        if(request()->isMethod('get') && isset(request()->id)){
            $product_rosource['has_property_options'] = $has_property_options;
        }
        return $product_rosource;
    }
}
