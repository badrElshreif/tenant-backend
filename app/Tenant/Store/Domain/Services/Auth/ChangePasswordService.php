<?php

namespace App\Tenant\Store\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Payloads\UnauthorizedPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Facades\Hash;

class ChangePasswordService extends Service
{
    public function handle($data = [])
    {

        try {
            // get authanticated user password
            $password = auth()->user()->password;
            // if oldPassword matched
            if (Hash::check($data['old_password'], $password)) {
                auth()->user()->update([
                    'password' => Hash::make($data['password']),
                ]);

                return new GenericPayload(
                    ['message' => __('success.passwordUpdated')]
                );
            }
            // if oldPassword doesn'\t match
            return new GenericPayload(
                __('error.wrongPassword'), 422
            );
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
