<?php

namespace App\Tenant\AppContent\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqLiteResource extends JsonResource
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
            'question' => $this->question,
            'answer' => $this->answer
        ]);
    }
}
