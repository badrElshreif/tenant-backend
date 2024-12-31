<?php

namespace App\Tenant\Admin\Actions\Auth;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Admin\Domain\Services\Auth\LogoutAdminService;

class LogoutAdminAction
{
    public function __construct(GenericResponder $responder, LogoutAdminService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(): \Illuminate\Http\JsonResponse
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}
