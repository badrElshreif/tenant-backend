<?php

namespace App\Tenant\Store\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;

class LogoutCenterService extends Service
{
    public function handle($data = [])
    {
        auth("center")->logout();
        return new GenericPayload(['message' => 'success']);
    }
}
