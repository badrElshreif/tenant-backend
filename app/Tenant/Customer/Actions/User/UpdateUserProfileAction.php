<?php

namespace App\User\Actions\User;

use App\User\Domain\Requests\UpdateUserProfileFormRequest;
use App\User\Domain\Services\User\UpdateUserProfileService;
use App\User\Responders\UserResponder;

class UpdateUserProfileAction
{
    public function __construct(UserResponder $responder, UpdateUserProfileService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(UpdateUserProfileFormRequest $request)
    {
        return $this->responder->withResponse(
           $this->service->handle($request->validated())
        )->respond();
    }
}
