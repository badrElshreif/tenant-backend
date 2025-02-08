<?php

namespace App\User\Actions\User;
use App\User\Domain\Services\User\ToggleUserStatusService;
use App\User\Responders\UserResponder;
use App\User\Domain\Requests\ToggleStatusUserFormRequest;

class ToggleUserStatusAction 
{
    public function __construct(UserResponder $responder, ToggleUserStatusService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ToggleStatusUserFormRequest $request, $id) 
    {
        return $this->responder->withResponse(
            $this->service->handle(
                array_merge($request->validated(),["user_id" => $id])
            )
        )->respond();
    }
}