<?php

namespace App\Tenant\Admin\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Infrastructure\Exceptions\UserNotFoundException;

class UpdateProfileService extends Service
{
    public function handle($data = [])
    {
        try {
                $admin = auth()->user();
                $admin->update($data);

        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
        return new GenericPayload($admin);
    }
}
