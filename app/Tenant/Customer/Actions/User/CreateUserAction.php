<?php

namespace App\User\Actions\User;

use App\User\Domain\Requests\UserRequest;
use App\User\Domain\Services\User\CreateUserService;
use App\User\Responders\UserResponder;

class CreateUserAction
{
    public function __construct(UserResponder $responder, CreateUserService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(UserRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
