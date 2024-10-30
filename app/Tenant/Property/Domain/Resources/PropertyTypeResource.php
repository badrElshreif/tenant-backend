<?php

namespace App\Property\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyTypeResource extends JsonResource
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
            'key' => $this->key,
            'name' => $this->name,
            'has_options' => $this->has_options
        ];
    }
}