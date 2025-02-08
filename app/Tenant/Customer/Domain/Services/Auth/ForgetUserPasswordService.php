<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\PasswordReset;
use App\User\Domain\Models\Customer;
use Carbon\Carbon;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ForgetUserPasswordService extends Service
{
    public function __construct()
    {

    }

    public function handle($data = [])
    {
        try {

            if (isset($data['type']) && $data['type'] == 'email') {
                $user = Customer::whereEmail($data['email'])->firstOrFail();

            } else {
                if (str_starts_with($data['phone'], '0')) {
                    $data['phone'] = substr($data['phone'], 1, 20);
                }
                $user = Customer::whereCountryCode($data['country_code'])->wherePhone($data['phone'])->firstOrFail();
                if ($user->phone_verified_at == null) {
                    return new GenericPayload(__('error.accountNotActivated'), 425
                    );
                }
            }

            if ($user->is_active == 1) {
                $token = rand(1111, 9999);
                if (isset($data['type']) && $data['type'] == 'email') {
                    PasswordReset::updateOrCreate(
                        ['phone' => $user->email], ['token' => $token, 'created_at' => Carbon::now()]
                    );

                    $user->update([
                        'verification_code' => $token,
                        'phone_verified_at' => Carbon::now(),
                    ]);
                    //$message = "<#>Your token is {$token}";

                    try {
                        if (app()->getLocale() == 'en') {
                            $title = "Change Password - Rkaayf";
                            $message = "According your request to reset password ,Code is {$token} use it , if you didn't ask ignore it";
                        } else {
                            $title = "تغير كلمة المرور - رأس الكيف";
                            $message = "بنــــاء ع طلبكم تغيير كلمة المرور استخدم كود  {$token} يرجى التجاهل في حالة عدم الطلب";
                        }
                        //send email
                        (new \App\Infrastructure\Domain\Services\SendGridEmailService())
                            ->sendMail($title, $data['email'], ['message' => $message], 'emails.change_password');
                        //  Mail::to($data['email'])->send(new \App\Admin\Domain\Mail\ForgetPasswordMail($message));

                    } catch (\Exception $exception) {
                        info("Forget mail", [$exception->getMessage()]);
                    }

                    return new GenericPayload(
                    // __('success.TokenSentToPhone'), 200
                        [
                            'message' => __('success.TokenSentToEmail'),
                            'code' => $token,
                            'type' => 'email'
                        ],
                        Response::HTTP_RESET_CONTENT
                    );
                } else {
                    PasswordReset::updateOrCreate(
                        ['phone' => $user->phone, 'country_code' => $data['country_code']], ['token' => $token, 'created_at' => Carbon::now()]
                    );

                    $user->update([
                        'verification_code' => $token,
                        'phone_verified_at' => Carbon::now(),
                    ]);
                    $message = "<#>Your token is {$token}";
                    send_msegat_sms($user->country_code . $user->phone, $message);
                    //send sms to user phone with token

                    return new GenericPayload(
                    // __('success.TokenSentToPhone'), 200
                        [
                            'message' => __('success.TokenSentToPhone'),
                            'code' => $token,
                            'type' => 'phone'
                        ],
                        Response::HTTP_RESET_CONTENT
                    );
                }


            }
            return new GenericPayload(
                __('error.inActiveUser'), 422
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
