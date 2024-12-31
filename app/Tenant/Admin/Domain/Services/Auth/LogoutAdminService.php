<?php

namespace App\Tenant\Admin\Domain\Services\Auth;

use App\Infrastructure\Domain\Services\Service;

class LogoutAdminService extends Service
{
    public function handle($data = []): array
    {
        auth("tenant-admin")->user()->token()->revoke();
        return [
            'status' => true,
            'message' => __('success.logout'),
        ];
    }
}
