<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Payloads\UnauthorizedPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordService extends Service
{
    public function handle($data = [])
    {

        if (auth("api")->check()) {
            // get authanticated user password
            $password = auth("api")->user()->password;
            // if oldPassword matched
            if (Hash::check($data['old_password'], $password)) {
                auth("api")->user()->update([
                    'password' => Hash::make($data['new_password']),
                ]);

                return new GenericPayload(
                    ['message' => __('success.passwordUpdated')],
                    Response::HTTP_RESET_CONTENT
                );
            }
            // if oldPassword doesn'\t match
            return new GenericPayload(
                __('error.wrongOldPassword'), 422
            );
        }
    }
}
