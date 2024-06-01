<?php

namespace App\Tenant\Location\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tenant\Location\Domain\Resources\CountryLiteResource;
class StateLiteResource extends JsonResource
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
            "id" => $this->id,
            'name' => $this->name,
            'country' => new CountryLiteResource($this->country),
            'is_active' => $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y')
        ];
    }
}
