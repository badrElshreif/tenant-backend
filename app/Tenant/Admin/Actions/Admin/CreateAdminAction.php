<?php

namespace App\Tenant\Admin\Actions\Admin;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Admin\Domain\Requests\AdminRequest;
use App\Tenant\Admin\Domain\Services\Admin\CreateAdminService;

class CreateAdminAction
{
    public function __construct(GenericResponder $responder, CreateAdminService $service)
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
