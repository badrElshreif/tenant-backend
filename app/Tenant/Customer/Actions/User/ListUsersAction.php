<?php

namespace App\User\Actions\User;

use App\User\Domain\Services\User\ListUsersService;
use App\User\Responders\UserResponder;

class ListUsersAction 
{
    public function __construct(UserResponder $responder, ListUsersService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke() 
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}