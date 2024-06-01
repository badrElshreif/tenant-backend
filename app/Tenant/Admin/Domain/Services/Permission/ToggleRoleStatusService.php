<?php

namespace App\Tenant\Admin\Domain\Services\Permission;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Role;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ToggleRoleStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $role = Role::findOrFail($data['role_id']);
            if($role->is_active && count($role->users()->get()) > 0)
                return new GenericPayload(
                    __('error.cannotDeactivate'), 422
                );
            $role->update([
                'is_active' => !$role->is_active
            ]);
            return new GenericPayload($role, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
