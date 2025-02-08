<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\PasswordReset;
use App\User\Domain\Models\Customer;
use Illuminate\Support\Facades\Hash;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ResetUserPasswordService extends Service
{
    public function __construct()
    {

    }

    public function handle($data = [])
    {
        try {

            if (isset($data['type']) && $data['type'] == 'email') {
                $user = Customer::whereEmail($data['email'])->firstOrFail();
                $check_token = PasswordReset::where(['phone' => $data['email'], 'token' => $data['token']])->first();
            } else {
                if (str_starts_with($data['phone'], '0')) {
                    $data['phone'] = substr($data['phone'], 1, 20);
                }

                $user = Customer::whereCountryCode($data['country_code'])->wherePhone($data['phone'])->firstOrFail();
                $check_token = PasswordReset::where(['phone' => $data['phone'], 'country_code' => $data['country_code'], 'token' => $data['token']])->first();

            }
            //$check_token = PasswordReset::where(['phone' => $data['phone'], 'token' => $data['token']])->first();
            //dd($check_token);
            if (!$check_token)
                return new GenericPayload(
                    __('error.invalidCode'), 422
                );

            $user = $user->update(['password' => Hash::make($data['password'])]);
            $check_token->delete();
            return new GenericPayload(['message' => __('success.PasswordChanged')], Response::HTTP_RESET_CONTENT);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
