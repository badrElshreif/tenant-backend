<?php

namespace App\Tenant\Admin\Actions\Admin;
use App\Tenant\Admin\Domain\Requests\AdminRequest;
use App\Tenant\Admin\Domain\Services\Admin\CreateAdminService;
use App\Tenant\Admin\Responders\AdminResponder;

class CreateAdminAction
{
    public function __construct(AdminResponder $responder, CreateAdminService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(AdminRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
