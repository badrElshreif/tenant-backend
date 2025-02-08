<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\PasswordReset;
use App\User\Domain\Models\Customer;
use Illuminate\Support\Facades\Hash;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class VerifyResetUserPasswordCodeService extends Service
{
    public function __construct()
    {

    }

    public function handle($data = [])
    {
        info("forget_password", $data);
        try {

            if ((isset($data['type']) && $data['type'] == 'email') || !empty($data['email'])) {
                $user = Customer::whereEmail($data['email'])->firstOrFail();
                $check_token = PasswordReset::where(['phone' => $data['email'], 'token' => $data['token']])->first();
                if (!$check_token)
                    return new GenericPayload(
                        __('error.invalidCode'), 422
                    );
                return new GenericPayload(['message' => __('success.validToken')], Response::HTTP_RESET_CONTENT);
            } else {
                if (str_starts_with($data['phone'], '0')) {
                    $data['phone'] = substr($data['phone'], 1, 20);
                }

                $user = Customer::whereCountryCode($data['country_code'])->wherePhone($data['phone'])->firstOrFail();
                $check_token = PasswordReset::where(['phone' => $data['phone'], 'token' => $data['token']])->first();
                if (!$check_token)
                    return new GenericPayload(
                        __('error.invalidCode'), 422
                    );
                return new GenericPayload(['message' => __('success.validToken')], Response::HTTP_RESET_CONTENT);
            }


        }catch (\Exception $ex) {
            info("VerifyResetUserPasswordCodeService", [$ex->getMessage()]);
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

//        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
//            throw new UserNotFoundException;
//        }
    }
}
