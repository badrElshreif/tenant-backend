<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserLiteResource;
use App\Product\Domain\Resources\ProductLiteResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Category\Domain\Resources\CategoryLiteResource;

class OrderServiceResource extends JsonResource
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
            'service' => new ProductLiteResource($this->service),
            'preview_fees' => $this->preview_fees,
            'service_price' => $this->service_price,
            'subcategory' => new CategoryLiteResource($this->subcategory),
            'problem_description' => $this->problem_description
        ];
    }
}
