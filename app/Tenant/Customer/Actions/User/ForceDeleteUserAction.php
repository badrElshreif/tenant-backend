<?php

namespace App\User\Actions\User;
use App\User\Domain\Services\User\DeleteUserService;
use App\User\Responders\UserResponder;

class ForceDeleteUserAction
{
    public function __construct(UserResponder $responder, DeleteUserService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->services->handle(["force_delete" => true])
        )->respond();
    }
}