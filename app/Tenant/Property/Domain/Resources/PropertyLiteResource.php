<?php

namespace App\Property\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;

class PropertyLiteResource extends JsonResource
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
            'name' => $this->name,
            'is_required' => $this->is_required,
            'has_options' => $this->propertyType->has_options,
            'options' => PropertyOptionLiteResource::collection($this->propertyOptions),
            'is_active' => $this->is_active,
            'property_type' => new GenericNameResource($this->propertyType),
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y')
        ];
    }
}