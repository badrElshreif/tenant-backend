<?php

namespace App\User\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLiteResource extends JsonResource
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
            'name' => $this->name,
            'country_code' => $this->country_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'verification_code' => $this->verification_code? : '',
        ]);
    }
}
