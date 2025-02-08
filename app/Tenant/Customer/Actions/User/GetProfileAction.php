<?php

namespace App\User\Actions\User;
use App\User\Domain\Services\User\GetProfileService;
use App\User\Responders\UserResponder;

class GetProfileAction
{
    public function __construct(UserResponder $responder, GetProfileService $services) 
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