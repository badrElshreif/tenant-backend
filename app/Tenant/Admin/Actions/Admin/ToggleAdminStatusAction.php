<?php

namespace App\Tenant\Admin\Actions\Admin;
use App\Tenant\Admin\Domain\Services\Admin\ToggleAdminStatusService;
use App\Tenant\Admin\Responders\AdminResponder;

class ToggleAdminStatusAction
{
    public function __construct(AdminResponder $responder, ToggleAdminStatusService $service)
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
