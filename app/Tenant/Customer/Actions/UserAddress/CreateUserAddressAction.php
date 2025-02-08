<?php

namespace App\User\Actions\UserAddress;

use App\User\Domain\Requests\UserAddressFormRequest;
use App\User\Domain\Services\UserAddress\CreateUserAddressService;
use App\User\Responders\UserAddressResponder;

class CreateUserAddressAction
{
    public function __construct(UserAddressResponder $responder, CreateUserAddressService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke(UserAddressFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
    //`title`, `user_id`, `country_id`, `state_id`, `city_id`, `address`, `phone
}
