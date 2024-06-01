<?php

namespace App\Tenant\Admin\Domain\Services\Permission;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class AssignPermissionsToAdminService extends Service
{
    public function handle($data = [])
    {
        try {
            $admin = Admin::findOrFail($data['admin_id']);
            //https://spatie.be/docs/laravel-permission/v3/basic-usage/direct-permissions
            //revoke & add new permissions:
            $admin->syncPermissions($data['permissions']);
            // assign permissions
            //$admin->givePermissionTo([$permission_id1, $permission_id2]);
            //revoke permission
            //$admin->revokePermissionTo('edit articles');
            return new GenericPayload($admin, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }
    }
}
