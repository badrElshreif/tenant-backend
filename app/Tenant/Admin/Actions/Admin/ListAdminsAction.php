<?php

namespace App\Tenant\Admin\Actions\Admin;
use App\Tenant\Admin\Domain\Requests\AdminRequest;
use App\Tenant\Admin\Domain\Services\Admin\ListAdminsService;
use App\Tenant\Admin\Responders\AdminResponder;

class ListAdminsAction
{
    public function __construct(AdminResponder $responder, ListAdminsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(AdminRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request)
        )->respond();
    }
}
