<?php

namespace App\Tenant\AppContent\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge([
            'id' => $this->id,
            'name' => $this->name
        ]);
    }
}
