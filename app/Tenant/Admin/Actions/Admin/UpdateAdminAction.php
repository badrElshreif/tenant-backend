<?php

namespace App\Tenant\Admin\Actions\Admin;
use App\Tenant\Admin\Domain\Requests\AdminRequest;
use App\Tenant\Admin\Domain\Services\Admin\UpdateAdminService;
use App\Tenant\Admin\Responders\AdminResponder;

class UpdateAdminAction
{
    public function __construct(AdminResponder $responder, UpdateAdminService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(AdminRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["admin_id" => $id]))
        )->respond();
    }
}
