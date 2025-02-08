<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\ForgetUserPasswordFormRequest;
use App\User\Domain\Services\Auth\ForgetUserPasswordService;
use App\User\Responders\UserResponder;

class ForgetUserPasswordAction
{
    public function __construct(UserResponder $responder, ForgetUserPasswordService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(ForgetUserPasswordFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
