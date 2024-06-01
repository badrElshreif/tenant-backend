<?php

namespace App\Tenant\Store\Actions\Auth;

use App\Tenant\Store\Domain\Services\Auth\LogoutCenterService;
use App\Admin\Responders\LogoutAdminResponder;

class LogoutCenterAction
{
    public function __construct(LogoutAdminResponder $responder, LogoutCenterService $services)
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
