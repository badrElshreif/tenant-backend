<?php

namespace App\User\Actions\Auth;
use App\User\Domain\Requests\LogoutUserFormRequest;
use App\User\Domain\Services\Auth\LogoutUserService;
use App\User\Responders\UserResponder;

class LogoutUserAction
{
    public function __construct(UserResponder $responder, LogoutUserService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }
    public function __invoke(LogoutUserFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
