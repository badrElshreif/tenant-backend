<?php

namespace App\Tenant\Store\Domain\Resources;

use App\Location\Domain\Resources\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Location\Domain\Resources\CityResource;


class StoreTempResource extends JsonResource
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
            'image' => $this->image,
            'id'  => $this->id,
            'name' => $this->name2??$this->name,
            'username' => $this->username,
            'type' => $this->type,
            'phone' => $this->phone,
            'email' => $this->email,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'bank_name' => $this->bank_name,
            'iban' => $this->iban_no,
            'swift_code' => $this->swift_code,
            'bank_account_no' => $this->bank_account_no,
            'bank_user_name' => $this->bank_user_name,
            'bank_country_id' => $this->bank_country_id,
            'bank_country' => new CountryResource($this->bankCountry),
            'bank_type' => $this->bank_type,
            'commercial_registry_no' => $this->commercial_registry_no,
            'commercial_registry_photo' => $this->commercial_registry_photo,
            'city' => new CityResource($this->city),
            'store_id' => $this->store_id,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}
