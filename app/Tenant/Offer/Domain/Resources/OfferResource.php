<?php

namespace App\Tenant\Offer\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Product\Domain\Resources\ProductLiteResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'ar' => $this->translate('ar')->only('name'),
            'en' => $this->translate('en')->only('name'),
            'type' => $this->type,
            'value' => $this->value,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'store_id' => $this->store_id,
            //'products' => $this->products->pluck('id'),
            'products' => ProductLiteResource::collection($this->products),
            'free_product' => new ProductLiteResource($this->freeProduct),
            'free_product_id' => $this->free_product_id,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}
