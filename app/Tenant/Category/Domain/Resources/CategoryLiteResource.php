<?php

namespace App\Tenant\Category\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Infrastructure\Domain\Resources\GenericNameResource;

class CategoryLiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y')
        ];

        if ($this->parent_id != null) {
            $resource['category'] = new GenericNameResource($this->parent);
            //$resource['tax_percentage'] = $this->tax_percentage;
        }
        return $resource;
    }
}
