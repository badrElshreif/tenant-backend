<?php

namespace App\User\Actions\UserAddress;
use App\User\Domain\Services\UserAddress\ListUserAddressesService;
use App\User\Responders\UserAddressResponder;

class ListUserAddressesAction 
{
    public function __construct(UserAddressResponder $responder, ListUserAddressesService $services) 
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