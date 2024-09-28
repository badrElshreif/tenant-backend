<?php

namespace App\Tenant\AppContent\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Property\Domain\Resources\PropertyTypeResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource =  [
            'key' => $this->key,
            'body' => $this->body
        ];
        if(auth()->guard('admin')->check())
            return array_merge($resource, [
               // 'id' => $this->id,
                'name' => $this->name,
                //'is_active' => $this->is_active,
                'property_type' => new PropertyTypeResource($this->propertyType)
            ]);
        return $resource;
    }
}
