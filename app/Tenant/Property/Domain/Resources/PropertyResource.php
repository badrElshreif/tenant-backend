<?php

namespace App\Property\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Property\Domain\Resources\PropertyOptionResource;
use App\Property\Domain\Resources\PropertyTypeResource;
use App\Category\Domain\Resources\CategoryResource;

class PropertyResource extends JsonResource
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
            'ar' => $this->translate('ar')->only('name'),
            'en' => $this->translate('en')->only('name'),
            'is_active' => $this->is_active,
            'is_required' => $this->is_required,
            'has_options' => $this->propertyType->has_options,
            'options' => PropertyOptionResource::collection($this->propertyOptions),
            'categories' => CategoryResource::collection($this->categories),
            'property_type' => new PropertyTypeResource($this->propertyType),
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}