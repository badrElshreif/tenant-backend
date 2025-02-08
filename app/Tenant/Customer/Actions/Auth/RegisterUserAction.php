<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\RegisterUserFormRequest;
use App\User\Domain\Services\Auth\RegisterUserService;
use App\User\Responders\UserResponder;

class RegisterUserAction
{
    public function __construct(UserResponder $responder, RegisterUserService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(RegisterUserFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
