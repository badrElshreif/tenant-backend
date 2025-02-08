<?php

namespace App\User\Actions\User;
use App\User\Domain\Services\User\DeleteUserService;
use App\User\Responders\UserResponder;

class DeleteUserAction 
{
    public function __construct(UserResponder $responder, DeleteUserService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id) 
    {
        return $this->responder->withResponse(
            $this->services->handle(["user_id" => $id])
        )->respond();
    }
}