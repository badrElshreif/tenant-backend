<?php

namespace App\Tenant\Admin\Actions\Admin;
use App\Tenant\Admin\Domain\Services\Admin\GetAdminService;
use App\Tenant\Admin\Responders\AdminResponder;

class GetAdminAction
{
    public function __construct(AdminResponder $responder, GetAdminService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["admin_id" => $id])
        )->respond();
    }
}
