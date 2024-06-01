<?php

namespace App\Tenant\Admin\Domain\Services\Permission;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Permission;
use App\Tenant\Admin\Domain\Filters\PermissionFilter;
use App\Tenant\Admin\Domain\Resources\PermissionResource;
use Symfony\Component\HttpFoundation\Response;

class ListPermissionsService extends Service
{
    protected $permission, $filter;

    public function __construct(Permission $permission, PermissionFilter $filter)
    {
        $this->permission = $permission;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        try {
            $guard = 'admin';
            if(auth()->guard('store')->check() || auth()->guard('center')->check()){
                $store_id = auth()->user()->store_id;
                $guard = auth()->user()->store->type == 'stores' ? 'store' : 'center';
            }
            $permissions = $this->permission->whereGuardName($guard)->get()->groupBy('key');
            $perms_array = [];

            foreach ($permissions as $item_key => $item) {
                $perms = [];
                if(count($item) > 0){
                    foreach($item as $key => $perm){
                        array_push($perms, new PermissionResource($perm));
                    }
                    $permission_obj=new \stdClass();
                    // $permission_obj->ar = (object) ['display_name' => optional($item[0]->translate('ar'))->key_name];
                    // $permission_obj->en = (object) ['display_name' => optional($item[0]->translate('en'))->key_name];
                    $permission_obj->display_name = optional($item[0]->translate(app()->getLocale()))->key_name;
                    $permission_obj->permissions=$perms;
                    array_push($perms_array,$permission_obj);
                }
            }

            // $permissions = $permissions->map(function($item) use ($perms){

            //     foreach($item as $key => $perm){
            //         $item[$key] = new PermissionResource($perm);
            //     }
            //     return $item;

            // });

            return new GenericPayload($perms_array, Response::HTTP_OK);
        } catch (Exception $ex) {
            return new GenericPayload(
                ('error.someThingWrong'), 422
            );
        }
    }
}
