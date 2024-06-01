<?php

namespace App\Tenant\Admin\Actions\Auth;

use App\Tenant\Admin\Domain\Requests\ChangePasswordFormRequest;
use App\Tenant\Admin\Domain\Services\Auth\ChangePasswordService;
use App\Tenant\Admin\Responders\ChangePasswordResponder;

class ChangePasswordAction
{
    public function __construct(ChangePasswordResponder $responder, ChangePasswordService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(ChangePasswordFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
