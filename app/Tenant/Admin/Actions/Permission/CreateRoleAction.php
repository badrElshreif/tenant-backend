<?php

namespace App\Tenant\Admin\Actions\Permission;
use App\Tenant\Admin\Domain\Requests\RoleRequest;
use App\Tenant\Admin\Domain\Services\Permission\CreateRoleService;
use App\Tenant\Admin\Responders\RoleResponder;

class CreateRoleAction
{
    public function __construct(RoleResponder $responder, CreateRoleService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(RoleRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
