<?php

namespace App\Tenant\Product\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tenant\Product\Domain\Models\ProductView;
use App\Offer\Domain\Resources\OfferLiteResource;
use App\Tenant\Product\Domain\Resources\ProductPropertyLiteResource;
use App\Uploader\Domain\Resources\AttachmentResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $category = $this->category->type == 'stores' ? $this->category->parent : $this->category;
        $discount = 0.000;
        $free_product = null;
        $offer = $this->offer->first();

        if ($offer && $this->category->type == 'stores'){
            if ($offer->type == "free_product"){
                $free_product = new GenericNameResource($offer->freeProduct);
            }else if ($offer->type == 'percentage'){
                $discount = $this->price_including_tax * $offer->value / 100;
            }else{
                $discount = $offer->value;
            }
        }

        $similar_products = null;
        $reviews = [];
        $review_statistics = [];

        $similar_products =  ProductView::active(1)->whereCategoryId($this->category_id)->where('id', '!=', $this->id)->orderBy('id', 'desc')->limit(10)->get();
            $reviews = RatingResource::collection($this->ratings()->where('is_active', 1)->get());
            $review_statistics = [];
            for ($i=5; $i > 0; $i--) {
                $rate = array(
                    'rate' => $i,
                    'users_no' => $this->ratings->where('rate', '>', $i-1)->where('rate', '<=', $i)->where('is_active', 1)->count('user_id')
                );
                array_push($review_statistics, $rate);
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
        $last_review = $this->ratings()->where('is_active', 1)->orderBy('id', 'desc')->first();

         $attachments = $this->attachments;
        if(\Request::route()->getName() == "products.show")
            $attachments = optional($this->product)->attachments;

        $resource =  [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'tags' => $this->tags,
            'is_active' => $this->is_active,
            // 'deactivation_start_date' => $this->deactivation_start_date,
            // 'deactivation_end_date' => $this->deactivation_end_date,
            'ar' => $this->translate('ar') ? $this->translate('ar')->only('name', 'description', 'tags') : null,
            'en' => $this->translate('en') ? $this->translate('en')->only('name', 'description', 'tags') :null,
            'image' => $this->image,
            'catalog' => $this->catalog,
            //'attachments' => AttachmentResource::collection($this->attachments),
            'attachments' => AttachmentResource::collection($attachments),
            'category' => new GenericNameResource($category),
            'store' => new GenericNameResource($this->store),
            'price' => $this->price,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'rate' => round($this->ratings->where('is_active', 1)->avg('rate'), 2),
            'rating_users_no' => $this->ratings->where('is_active', 1)->count('user_id'),
            'rating_statistics' => $review_statistics,
            'is_favourite' => $is_favourite,
            'is_rated' => $is_rated,
            //'sales_count' => $this->orders->count('product_id'),
            'reviews' => $reviews,
            'last_review' => new RatingResource($last_review),
        ];

        if($this->category->type == 'centers')
            $resource['preview_fees'] = $this->preview_fees;

        if($this->category->type == 'stores'){
            $resource = array_merge($resource, [
                'offer' => new OfferLiteResource($this->offer->first()),
                'subcategory' => new GenericNameResource($this->category),
                'brand' => new GenericNameResource($this->brand),
                'quantity' => intval($this->quantity),
                'price_including_tax' => number_format((float)$this->price_including_tax, 2, '.', ''),
                'price_after_discount' => number_format((float)($this->price_including_tax - $discount), 2, '.', ''),
                'discount' => $discount > 0 ?number_format((float)($discount/$this->price_including_tax)*100, 2, '.', '') : 0.00,
                'free_product' => $free_product,
                'barcode' => $this->barcode,
                'max_purchase_quantity' => $this->max_purchase_quantity,
                'made_in' => new GenericNameResource($this->country),
                'properties' => ProductPropertyLiteResource::collection($this->properties),
            ]);
        }
        return $resource;
    }
}
