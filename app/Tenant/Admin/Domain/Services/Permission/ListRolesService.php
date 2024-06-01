<?php

namespace App\Tenant\Admin\Domain\Services\Permission;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Role;
use App\Tenant\Admin\Domain\Filters\RoleFilter;
use Symfony\Component\HttpFoundation\Response;

class ListRolesService extends Service
{
    protected $role, $filter;

    public function __construct(Role $role, RoleFilter $filter)
    {
        $this->role = $role;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $store_id = null;
        $guard = 'admin';
        if(auth()->guard('store')->check() || auth()->guard('center')->check()){
            $store_id = auth()->user()->store_id;
            $guard = auth()->user()->store->type == 'stores' ? 'store' : 'center';
        }

        // $order = request()->orderBy ? : 'id';
        // $order_type = request()->orderBy ? 'ASC' : 'DESC';

        $collection = $this->role->whereGuardName($guard)
            ->when($store_id, function($collection) use ($store_id){
                return $collection->where('roles.store_id', $store_id);
            });

        if( isset(request()->is_paginated) && request()->is_paginated == 0 ):
            $roles = $collection
            // ->orderBy($order, $order_type)
            ->filter($this->filter)->get();
            return new GenericPayload($roles, Response::HTTP_OK);
        else:
            $roles = $collection->whereNotIn('name', ['super admin', 'super-admin'])
            // ->orderBy($order, $order_type)
            ->filter($this->filter)->paginate(config('app.pagination_limit'));
            return new GenericPayload($roles, Response::HTTP_ACCEPTED);
        endif;
    }
}
