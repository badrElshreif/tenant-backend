<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserLiteResource;
use App\Product\Domain\Resources\ProductLiteResource;
use App\Warranty\Domain\Resources\WarrantyLiteResource;
class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $total = ($this->product_price + $this->warranty_price - $this->offer_discount) *$this->quantity;
        return [
            'id' => $this->id,
            'product' => new ProductLiteResource($this->product),
            'warranty' => new WarrantyLiteResource($this->warranty),
            'quantity' => $this->quantity,
            'product_price' => $this->product_price,
            'warranty_price' => $this->warranty_price,
            'offer_discount' => $this->offer_discount,
            'price_after_discount' => number_format((float)($this->product_price - $this->offer_discount), 3, '.', ''),
            'total' => number_format((float)$total, 3, '.', ''),
            'free_product' => new ProductLiteResource($this->freeProduct),
        ];
    }
}
