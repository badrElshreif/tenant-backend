<?php

namespace App\Property\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyOptionLiteResource extends JsonResource
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
            'name' => $this->name
        ];
    }
}