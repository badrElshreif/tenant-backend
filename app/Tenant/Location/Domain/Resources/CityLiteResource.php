<?php

namespace App\Tenant\Location\Domain\Resources;

use App\Infrastructure\Collections\CustomJsonResource;
use DB;
use App\Tenant\Location\Domain\Resources\StateResource;

class CityLiteResource extends CustomJsonResource
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
            'state' => new StateLiteResource($this->state),
            'is_active' => $this->is_active,
            "country_id" => $this->country_id,
            "state_id" => $this->state_id,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}
