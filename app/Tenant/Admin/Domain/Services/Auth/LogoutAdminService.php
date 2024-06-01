<?php

namespace App\Tenant\Admin\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;

class LogoutAdminService extends Service
{
    public function handle($data = [])
    {
        auth("admin")->logout();
        return new GenericPayload(['message' => 'success']);
    }
}
