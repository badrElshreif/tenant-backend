<?php

namespace App\Tenant\Admin\Actions\Permission;
use App\Tenant\Admin\Domain\Requests\RoleRequest;
use App\Tenant\Admin\Domain\Services\Permission\ListRolesService;
use App\Tenant\Admin\Responders\RoleResponder;

class ListRolesAction
{
    public function __construct(RoleResponder $responder, ListRolesService $service)
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
