<?php

namespace App\User\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserAddressResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //substr(string,start,length)
        // $country_code_length = strlen($this->country_code);;
        // $phone = substr($this->phone,$country_code_length,20);
        
        return array_merge([
            'id' => $this->id,
            'name' => $this->name,
            'country_code' => $this->country_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'orders_count' => $this->orders_count,
            'wallet' => optional($this->transactions()->orderBy('id', 'desc')->first())->wallet_total ? : 0.00,
            'last_login_at' => \Carbon\Carbon::parse($this->last_login_at)->translatedFormat('d M Y'),
            'addresses' => UserAddressResource::collection($this->addresses),
            'verification_code' => $this->verification_code,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            //'firebaseTokens' => $this->tokens->pluck('device_token')->all()
        ]);
    }
}
