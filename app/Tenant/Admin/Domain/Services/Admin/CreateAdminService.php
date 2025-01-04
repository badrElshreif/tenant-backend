<?php

namespace App\Tenant\Admin\Domain\Services\Admin;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Tenant\Admin\Domain\Resources\AdminResource;
use App\Tenant\Store\Domain\Models\CenterAdmin;
use App\Tenant\Store\Domain\Models\StoreAdmin;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CreateAdminService extends Service
{
    public function handle($data = [])
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = isset($data['is_active']) ? $data['is_active'] : 1;

        if (auth()->guard('store')->check() || auth()->guard('center')->check()) {
            $data['store_id'] = auth()->user()->store_id;
            if (auth()->user()->store->type == 'stores')
                $admin = StoreAdmin::create($data);
            else
                $admin = CenterAdmin::create($data);
        } else {
            $admin = Admin::create($data);
        }

        if (isset($data['roles']))
            $admin->syncRoles($data['roles']);

        return [
            'data' => new AdminResource($admin),
            'status' => true,
            'message' => __('success.createdSuccessfully'),
        ];
    }
}
