<?php

namespace App\Tenant\Admin\Domain\Services\Permission;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Role;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;

class UpdateRoleService extends Service
{
    public function handle($data = [])
    {
        try {
            $role = Role::findOrFail($data['role_id']);
            if(isset($data['is_active']) && $data['is_active'] == 0){
                if(count($role->users()->get()) > 0)
                		return new GenericPayload(
                            __('error.cannotDeactivate'), 422
                        );
            }

            $role_unique_check = Role::where('guard_name', $role->guard_name)
                ->whereHas('translations', function($q) use ($data) {
                    $q->whereIn('display_name', Arr::only($data, ['ar', 'en']));
                })->where('id', '!=', $role->id)->first();

            if($role_unique_check)
                return new GenericPayload(__('error.nameIsRepeated'), 422);

            $role->update($data);
            if(isset($data['permissions'])){
                $role->syncPermissions($data['permissions']);
                $role->givePermissionTo($data['permissions']);
            }

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
