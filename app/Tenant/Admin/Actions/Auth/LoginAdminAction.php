<?php

namespace App\Tenant\Admin\Actions\Auth;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Admin\Domain\Requests\LoginAdminFormRequest;
use App\Tenant\Admin\Domain\Services\Auth\LoginAdminService;

class LoginAdminAction
{
    private $responder;
    private $services;

    public function __construct(GenericResponder $responder, LoginAdminService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(LoginAdminFormRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
