<?php

namespace App\User\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Location\Domain\Resources\CityLiteResource;
use DB;

class UserAddressResource extends JsonResource
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
            'title' => $this->title,
            'phone' => $this->phone,
            'is_primary' => $this->is_primary,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'nearest_landmarks' => $this->nearest_landmarks,
            //'city_id' => $this->city_id,
            'city' => new CityLiteResource($this->city),
            //'country' => $this->country_id
        ];
    }
}
