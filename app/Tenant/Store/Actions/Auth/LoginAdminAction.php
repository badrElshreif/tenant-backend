<?php

namespace App\Tenant\Store\Actions\Auth;

use App\Admin\Domain\Requests\LoginAdminFormRequest;
use App\Tenant\Store\Domain\Services\Auth\LoginAdminService;
use App\Admin\Responders\LoginAdminResponder;

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
