<?php

namespace App\Property\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyOptionResource extends JsonResource
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
            'ar' => $this->translate('ar')->only('name'),
            'en' => $this->translate('en')->only('name')
        ];
    }
}