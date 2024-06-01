<?php

namespace App\Tenant\Admin\Actions\Auth;

use App\Tenant\Admin\Domain\Requests\ForgetPasswordRequest;
use App\Tenant\Admin\Domain\Services\Auth\ForgetPasswordService;
use App\Tenant\Admin\Responders\ChangePasswordResponder;

class ForgetPasswordAction
{
    public function __construct(ChangePasswordResponder $responder, ForgetPasswordService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }
    public function __invoke(ForgetPasswordRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
