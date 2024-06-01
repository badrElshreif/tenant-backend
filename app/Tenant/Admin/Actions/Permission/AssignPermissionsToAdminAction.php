<?php

namespace App\Tenant\Admin\Actions\Permission;

use App\Tenant\Admin\Domain\Requests\AssignPermissionsToAdminFormRequest;
use App\Tenant\Admin\Domain\Services\API\AssignPermissionsToAdminService;
use App\Tenant\Admin\Responders\API\AssignPermissionsToAdminResponder;

class AssignPermissionsToAdminAction
{
    public function __construct(AssignPermissionsToAdminResponder $responder, AssignPermissionsToAdminService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(AssignPermissionsToAdminFormRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["admin_id" => $id]))
        )->respond();
    }
}
