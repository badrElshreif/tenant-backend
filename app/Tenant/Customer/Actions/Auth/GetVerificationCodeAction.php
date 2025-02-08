<?php

namespace App\User\Actions\Auth;

use App\User\Domain\Requests\ResendVerificationCodeFormRequest;
use App\User\Domain\Services\Auth\GetVerificationCodeService;
use App\User\Responders\UserResponder;

class GetVerificationCodeAction
{

    private $responder;
    private $service;


    public function __construct( UserResponder $responder, GetVerificationCodeService $service ) {
        $this->responder = $responder;
        $this->service = $service;
    }


    public function __invoke(ResendVerificationCodeFormRequest $request){
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}