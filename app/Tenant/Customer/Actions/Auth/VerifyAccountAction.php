<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\VerifyAccountFormRequest;
use App\User\Domain\Services\Auth\VerifyAccountService;
use App\User\Responders\UserResponder;

class VerifyAccountAction
{

    private $responder;
    private $verifyAccountService;


    public function __construct(UserResponder $responder, VerifyAccountService $verifyAccountService) {
        $this->responder = $responder;
        $this->verifyAccountService = $verifyAccountService;
    }


    public function __invoke(VerifyAccountFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->verifyAccountService->handle($request->validated())
        )->respond();
    }
}