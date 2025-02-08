<?php

namespace App\User\Actions\User;
use App\User\Domain\Services\User\ListUserAddressesService;
use App\User\Responders\ListUserAddressesResponder;

class ListUserAddressesAction 
{
    public function __construct(ListUserAddressesResponder $responder, ListUserAddressesService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id) 
    {
        return $this->responder->withResponse(
            $this->service->handle(["user_id" => $id])
        )->respond();
    }
}