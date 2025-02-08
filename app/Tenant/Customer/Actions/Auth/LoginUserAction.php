<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\LoginUserFormRequest;
use App\User\Domain\Services\Auth\LoginUserService;
use App\User\Responders\UserResponder;

class LoginUserAction
{
    public function __construct(UserResponder $responder, LoginUserService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(LoginUserFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
