<?php

namespace App\User\Actions\User;

use App\User\Domain\Requests\UpdatePhoneFormRequest;
use App\User\Domain\Services\User\UpdatePhoneService;
use App\User\Responders\UserResponder;

class UpdatePhoneAction
{

    private $responder;
    private $service;


    public function __construct(UserResponder $responder, UpdatePhoneService $service
    ) {
        $this->responder = $responder;
        $this->service = $service;
    }


    public function __invoke(UpdatePhoneFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}