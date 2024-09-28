<?php

namespace App\Tenant\AppContent\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $page = [
            'slug' => $this->slug,
            'title' => $this->title,
            'body' => $this->body
        ];
        if(auth()->guard('admin')->check())
            return array_merge($page, [
                'ar' => $this->translate('ar')->only('title', 'body'),
                'en' => $this->translate('en')->only('title', 'body'),
            ]);
        return $page;

    }
}
