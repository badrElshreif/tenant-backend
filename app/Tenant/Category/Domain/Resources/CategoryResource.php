<?php

namespace App\Tenant\Category\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Infrastructure\Domain\Resources\GenericNameResource;

class CategoryResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'ar' => optional($this->translate('ar'))->only('name', 'description'),
            'en' => optional($this->translate('en'))->only('name', 'description'),
            'is_active' => (bool) $this->is_active,
            'parent_id' => $this->parent_id,
            'image' => $this->image,
            'order' => $this->order,
            'type' => $this->type,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y')
        ];

        if ($this->parent_id != null) {
            $resource['category'] = new GenericNameResource($this->parent);
            //$resource['tax_percentage'] = $this->tax_percentage;
        }
        return $resource;
    }
}
