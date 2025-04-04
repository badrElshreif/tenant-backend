<?php

namespace App\Tenant\Brand\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $translations = [
            'ar' => optional($this->translate('ar'))->only('name', 'description'),
            'en' => optional($this->translate('en'))->only('name', 'description')
        ];

        $resource =  [
            'id' => $this->id,
            'name' => $this->name,
            'translations' => $translations,
            'ar' => $translations['ar'],
            'en' => $translations['en'],
            'is_active' => $this->is_active,
            'image' => $this->getLogoUrl(300,300),
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y')
        ];
        return $resource;
    }
}
