<?php

namespace App\User\Actions\User;
use App\User\Domain\Services\User\ShowUserService;
use App\User\Responders\UserResponder;

class ShowUserAction 
{
    public function __construct(UserResponder $responder, ShowUserService $services) 
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