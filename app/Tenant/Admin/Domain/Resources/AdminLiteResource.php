<?php

namespace App\Tenant\Admin\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tenant\Admin\Domain\Resources\PermissionResource;
use App\Tenant\Admin\Domain\Resources\RoleResource;
use App\School\Domain\Resources\SchoolLiteResource;
use Carbon\Carbon;
class AdminLiteResource extends JsonResource
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
            'id'  => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => (bool) $this->is_active,
            'avatar' => $this->avatar,
            'image' => optional($this->store)->image,
            'ar' => optional($this->store)->translate('ar') ? $this->store->translate('ar')->only('name') : null,
            'en' => optional($this->store)->translate('en') ? $this->store->translate('en')->only('name') :null,
            'store_id' => $this->store_id,
            'created_at' => Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'permissions' => $this->getPermissionsViaRoles()->pluck('name'),
            'roles' => RoleResource::collection($this->roles),
            'is_super_admin' => $this->hasRole('super admin') || $this->hasRole('super-admin')
            //'permissions' => array_unique($this->getPermissionsViaRoles()->pluck('name')->toArray()),
        ];
    }
}
