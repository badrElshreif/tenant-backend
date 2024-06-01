<?php

namespace App\Tenant\Store\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Location\Domain\Resources\CityResource;
use App\Tenant\Stores\Domain\Resources\GroupResource;
use App\Tenant\Stores\Domain\Resources\PackageResource;

class StoreLiteResource extends JsonResource
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
            'id'  => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'image' => $this->image,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'products' => $this->products()->count(),
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}
