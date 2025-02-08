<?php

namespace App\User\Actions\User;

use App\User\Domain\Requests\ChangePasswordFormRequest;
use App\User\Domain\Services\User\ChangePasswordService;
use App\User\Responders\UserResponder;

class ChangePasswordAction
{
    public function __construct(UserResponder $responder, ChangePasswordService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(ChangePasswordFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
