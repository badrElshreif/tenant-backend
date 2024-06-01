<?php

namespace App\Tenant\Store\Domain\Resources;

use App\Location\Domain\Resources\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Location\Domain\Resources\CityResource;


class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

    $setting = \App\AppContent\Domain\Models\Setting::whereIn('key', ['application_dues', 'delivery_charge'])->select('key', 'body')->get();

        return [
            'image' => $this->image,
            'id'  => $this->id,
            'name' => $this->name,
            'ar'=> ['name'=>$this->translations->where('locale','ar')->first()->name?? ''],
            'en'=> ['name'=>$this->translations->where('locale','en')->first()->name?? ''],
            'username' => $this->username,
            'type' => $this->type,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'site_name' => $this->site_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'bank_name' => $this->bank_name,
            'iban' => $this->iban_no,
            'swift_code' => $this->swift_code,
            'bank_account_no' => $this->bank_account_no,
            'bank_user_name' => $this->bank_user_name,
            'seller_id' => $this->seller_id,
            'bank_country_id' => $this->bank_country_id,
            'bank_country' => new CountryResource($this->bankCountry),
            'bank_type' => $this->bank_type,
            'bank_address' => $this->bank_address,
            'bank_user_phone' => $this->bank_user_phone,
            'commercial_registry_no' => $this->commercial_registry_no,
            'commercial_registry_photo' => $this->commercial_registry_photo,
            'city' => new CityResource($this->city),
            'state_id' => $this->city->state_id??null,
            'city_id' => $this->city_id??null,
            'country_id' => $this->city->country_id??null,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'has_delivery_service' => $this->has_delivery_service,
            'delivery_charge' => floatval($this->delivery_charge),
            'application_dues' => floatval($this->application_dues),
            'settings' => $setting,

        ];
    }
}
