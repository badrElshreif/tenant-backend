<?php

namespace App\Tenant\Admin\Actions\Permission;

use App\Tenant\Admin\Domain\Services\Permission\ListPermissionsService;
use App\Tenant\Admin\Responders\PermissionResponder;

class ListPermissionsAction
{
    public function __construct(PermissionResponder $responder, ListPermissionsService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}
