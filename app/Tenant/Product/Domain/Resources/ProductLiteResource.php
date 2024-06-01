<?php

namespace App\Tenant\Product\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tenant\Product\Domain\Resources\CategoryLiteResource;
use App\Tenant\Product\Domain\Resources\BrandLiteResource;
use App\Tenant\Product\Domain\Resources\OfferLiteResource;
use App\Tenant\Product\Domain\Resources\ProductPropertyResource;
use App\Uploader\Domain\Resources\AttachmentResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;
class ProductLiteResource extends JsonResource
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

        $resource = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'catalog' => $this->catalog,
            'category' => new GenericNameResource($category),
            'store' => new GenericNameResource($this->store),
            'price' => $this->price,
            'is_favourite' => $is_favourite,
            'is_active' => $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'rate' => round($this->ratings->where('is_active', 1)->avg('rate'), 2),
            'is_rated' => $is_rated,
            'sale_qty' => $this->sales_quantity,
            'distance' => $this->distance
        ];
        if($this->category->type == 'centers')
            $resource['preview_fees'] = $this->preview_fees;

        if($this->category->type == 'stores'){
            $resource = array_merge($resource, [
               // 'offer' => new GenericNameResource($this->offer->first()),
                'subcategory' => new GenericNameResource($this->category),
                'brand' => new GenericNameResource($this->brand),
                'quantity' => intval($this->quantity),
                'price_including_tax' => number_format((float)$this->price_including_tax, 2, '.', ''),
                'price_after_discount' => number_format((float)($this->price_including_tax - $discount), 2, '.', ''),
                'discount' => $discount > 0 ?number_format((float)($discount/$this->price_including_tax)*100, 2, '.', '') : 0.00,
                'barcode' => $this->barcode,
                'max_purchase_quantity' => $this->max_purchase_quantity,
                'made_in' => new GenericNameResource($this->country),
            ]);
        }
        return $resource;
    }
}
