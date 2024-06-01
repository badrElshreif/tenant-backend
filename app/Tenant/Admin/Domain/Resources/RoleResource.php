<?php

namespace App\Tenant\Admin\Domain\Resources;
use App\Tenant\Admin\Domain\Resources\PermissionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'name' => $this->name,
            'display_name' => $this->display_name,
            'ar' => $this->translate('ar')->only('display_name'),
            'en' => $this->translate('en')->only('display_name'),
            'is_active' => (bool) $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
