<?php

namespace App\Tenant\Admin\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PermissionResource extends JsonResource
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
            "id" => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            // 'ar' => optional($this->translate('ar'))->only('display_name'),
            // 'en' => optional($this->translate('en'))->only('display_name'),
            //'is_active' => (bool) $this->is_active
        ];
    }
}
