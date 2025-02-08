<?php

namespace App\User\Actions\UserAddress;
use App\User\Domain\Services\UserAddress\DeleteUserAddressService;
use App\User\Responders\UserAddressResponder;

class DeleteUserAddressAction 
{
    public function __construct(UserAddressResponder $responder, DeleteUserAddressService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id) 
    {
        return $this->responder->withResponse(
            $this->services->handle(["address_id" => $id])
        )->respond();
    }
}