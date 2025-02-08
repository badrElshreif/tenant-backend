<?php

namespace App\User\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserAddressResource;

class UserClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge([
            'id' => $this->id,
            'ar' => optional($this->translate('ar'))->only('name'),
            'en' => optional($this->translate('en'))->only('name'),
            //'key' => $this->key,
            'min_orders_number' => $this->min_orders_number,
            'is_active' => $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ]);
    }
}
