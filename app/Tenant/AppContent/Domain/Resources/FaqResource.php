<?php

namespace App\Tenant\AppContent\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            'is_active' => $this->is_active,
            'ar' => $this->translate('ar')->only('question', 'answer'),
            'en' => $this->translate('en')->only('question', 'answer'),
        ]);
    }
}
