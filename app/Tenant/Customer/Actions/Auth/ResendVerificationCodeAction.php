<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\ResendVerificationCodeFormRequest;
use App\User\Domain\Services\Auth\ResendVerificationCodeService;
use App\User\Responders\UserResponder;

class ResendVerificationCodeAction
{

    private $responder;
    private $service;


    public function __construct( UserResponder $responder, ResendVerificationCodeService $service ) {
        $this->responder = $responder;
        $this->service = $service;
    }


    public function __invoke(ResendVerificationCodeFormRequest $request){
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}