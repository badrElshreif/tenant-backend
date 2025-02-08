<?php

namespace App\User\Actions\User;
use App\User\Domain\Requests\UserRequest;
use App\User\Domain\Services\User\UpdateUserService;
use App\User\Responders\UserResponder;

class UpdateUserAction 
{
    public function __construct(UserResponder $responder, UpdateUserService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(UserRequest $request, $id) 
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["user_id" => $id]))
        )->respond();
    }
}