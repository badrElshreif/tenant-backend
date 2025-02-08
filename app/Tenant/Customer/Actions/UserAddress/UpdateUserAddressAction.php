<?php

namespace App\User\Actions\UserAddress;
use App\User\Domain\Requests\UserAddressFormRequest;
use App\User\Domain\Services\UserAddress\UpdateUserAddressService;
use App\User\Responders\UserAddressResponder;

class UpdateUserAddressAction 
{
    public function __construct(UserAddressResponder $responder, UpdateUserAddressService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(UserAddressFormRequest $request, $id) 
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["address_id" => $id]))
        )->respond();
    }
}