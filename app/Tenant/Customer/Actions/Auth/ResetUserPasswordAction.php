<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\ResetUserPasswordFormRequest;
use App\User\Domain\Services\Auth\ResetUserPasswordService;
use App\User\Responders\UserResponder;

class ResetUserPasswordAction
{
    public function __construct(UserResponder $responder, ResetUserPasswordService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(ResetUserPasswordFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
