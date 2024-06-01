<?php

namespace App\Tenant\Store\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Domain\Models\PasswordReset;
use App\Admin\Domain\Models\Admin;
use Carbon\Carbon;
use App\Infrastructure\Exceptions\UserNotFoundException;

class ForgetPasswordService extends Service
{
    public function __construct()
    {

    }
    public function handle($data = [])
    {
        try {
        $admin = Admin::whereEmail($data['email'])->whereNotNull('store_id')->first();
        if(!$admin)
            return new GenericPayload( __('error.wrongEmail'), 425);

        $type = request('type', 'stores');
        if ($type == 'stores' && $admin->store->type != 'stores')
            return new GenericPayload( __('error.wrongEmail'), 425);

        if ($type == 'centers' && $admin->store->type != 'centers')
            return new GenericPayload( __('error.wrongEmail'), 425);

        if ($admin->is_active == 1) {
            //$token = 1234;
            $token = rand(1111,9999);
            PasswordReset::updateOrCreate(
                ['phone' => $admin->email], ['token' => $token, 'created_at' => Carbon::now()]
            );
            //$message = "<#>Your token is {$token}";
            if(app()->getLocale()== 'en'){
                $message = "According your request to reset password ,Code is {$token} use it , if you didn't ask ignore it";
            }else {
                $message  = "بنــــاء ع طلبكم تغيير كلنة المرور استخدم كود  {$token} يرجى التجاهل في حالة عدم الطلب";
            }
            //send email to school
            \Mail::to($admin->email)->send(new \App\Admin\Domain\Mail\ForgetPasswordMail($message));

            return new GenericPayload(
                // __('success.TokenSentToPhone'), 200
                [
                    'message' => __('success.TokenSentToEmail'),
                    'code'    => $token
                ]
            );
        }
        return new GenericPayload(
            __('error.accountNotActivated'), 425
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
