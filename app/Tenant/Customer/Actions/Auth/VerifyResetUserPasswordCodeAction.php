<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\VerifyResetUserPasswordCodeFormRequest;
use App\User\Domain\Services\Auth\VerifyResetUserPasswordCodeService;
use App\User\Responders\UserResponder;

class VerifyResetUserPasswordCodeAction
{
    public function __construct(UserResponder $responder, VerifyResetUserPasswordCodeService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(VerifyResetUserPasswordCodeFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
