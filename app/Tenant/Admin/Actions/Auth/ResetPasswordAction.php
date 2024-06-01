<?php

namespace App\Tenant\Admin\Actions\Auth;

use App\Tenant\Admin\Domain\Requests\ResetPasswordRequest;
use App\Tenant\Admin\Domain\Services\Auth\ResetPasswordService;
use App\Tenant\Admin\Responders\ChangePasswordResponder;

class ResetPasswordAction
{
    public function __construct(ChangePasswordResponder $responder, ResetPasswordService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }
    public function __invoke(ResetPasswordRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
