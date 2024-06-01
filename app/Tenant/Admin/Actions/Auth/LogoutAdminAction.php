<?php

namespace App\Tenant\Admin\Actions\Auth;

use App\Tenant\Admin\Domain\Services\Auth\LogoutAdminService;
use App\Tenant\Admin\Responders\LogoutAdminResponder;

class LogoutAdminAction
{
    public function __construct(LogoutAdminResponder $responder, LogoutAdminService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }
    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}
