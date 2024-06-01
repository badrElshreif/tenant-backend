<?php

namespace App\Tenant\Admin\Actions\Permission;
use App\Tenant\Admin\Domain\Requests\RoleRequest;
use App\Tenant\Admin\Domain\Services\Permission\UpdateRoleService;
use App\Tenant\Admin\Responders\RoleResponder;

class UpdateRoleAction
{
    public function __construct(RoleResponder $responder, UpdateRoleService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(RoleRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["role_id" => $id]))
        )->respond();
    }
}
