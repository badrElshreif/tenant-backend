<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use App\Infrastructure\Exceptions\UserNotFoundException;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use App\User\Domain\Models\PasswordReset;

class ResendVerificationCodeService extends Service
{
    private $user;

    public function __construct(Customer $user)
    {
        $this->user = $user;
    }

    public function handle($data = null)
    {
        try {

             if(isset($data['type']) && $data['type'] =='email'){
                    $user = $this->user->whereEmail($data["email"])->firstOrFail();
                    $check_token = PasswordReset::where(['phone' => $data['email'], 'token' => $user->verification_code])->first();

                    $verification_code = rand(1111, 9999);

                    if($check_token)
                        $check_token->update([
                            'token' => $verification_code
                        ]);

                    $user->update([
                        'verification_code' => $verification_code
                    ]);


                    // if($user->phone_verified_at != NULL)
                    //     return new GenericPayload(
                    //          __('error.alreadyVerifiedAccount'), 422
                    //     );
                    $message = __('general.sms.phoneVerificationCode').$verification_code;
                   // send_msegat_sms($user->country_code.$user->phone, $verification_code);

                   // sendSMS($user->country_code.$user->phone, $message);
                    //resend verification code
                    return new GenericPayload(
                        [
                            'message' => __('success.successfullyResentCode'),
                            'code'    => $verification_code
                        ],
                        Response::HTTP_RESET_CONTENT
                    );
             }else{
                if(str_starts_with($data['phone'], '0')){
                    $data['phone'] = substr($data['phone'],1,20);
                }

                $user = $this->user->whereCountryCode($data['country_code'])->wherePhone($data["phone"])->orWhere('updated_phone', $data['phone'])->firstOrFail();
                $check_token = PasswordReset::where(['phone' => $data['phone'], 'country_code' => $data['country_code'], 'token' => $user->verification_code])->first();

                $verification_code = rand(1111, 9999);

                if($check_token)
                    $check_token->update([
                        'token' => $verification_code
                    ]);

                $user->update([
                    'verification_code' => $verification_code
                ]);


                // if($user->phone_verified_at != NULL)
                //     return new GenericPayload(
                //          __('error.alreadyVerifiedAccount'), 422
                //     );
                $message = __('general.sms.phoneVerificationCode').$verification_code;
                send_msegat_sms($user->country_code.$user->phone, $verification_code);

               // sendSMS($user->country_code.$user->phone, $message);
                //resend verification code
                return new GenericPayload(
                    [
                        'message' => __('success.successfullyResentCode'),
                        'code'    => $verification_code
                    ],
                    Response::HTTP_RESET_CONTENT
                );
             }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
