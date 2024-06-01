<?php

namespace App\Tenant\Store\Actions\Auth;

use App\Admin\Domain\Requests\ResetPasswordRequest;
use App\Tenant\Store\Domain\Services\Auth\ResetPasswordService;
use App\Admin\Responders\ChangePasswordResponder;

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
