<?php

namespace App\Tenant\Admin\Actions\Auth;

use App\Tenant\Admin\Domain\Requests\LoginAdminFormRequest;
use App\Tenant\Admin\Domain\Services\Auth\LoginAdminService;
use App\Tenant\Admin\Responders\LoginAdminResponder;

class LoginAdminAction
{
    public function __construct(LoginAdminResponder $responder, LoginAdminService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(LoginAdminFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
