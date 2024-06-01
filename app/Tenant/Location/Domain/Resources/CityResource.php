<?php

namespace App\Tenant\Location\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DB;
use App\Tenant\Location\Domain\Resources\StateResource;

class CityResource extends JsonResource
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
            'ar' => optional($this->translate('ar'))->only('name'),
            'en' => optional($this->translate('en'))->only('name'),
            'state_id' => $this->state_id,
            'country_id' => $this->country_id,
            'state' => new StateResource($this->state),
            'is_active' => $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}
