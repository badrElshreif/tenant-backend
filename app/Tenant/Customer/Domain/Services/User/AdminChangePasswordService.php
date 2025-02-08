<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;

use App\User\Domain\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminChangePasswordService extends Service
{
    public function handle($data = [])
    {

        try {

            $user = Customer::findOrFail($data['id']);
            //info("aaa",[$user]);
            if ($data['type'] == 'mobile') {

            } else {
                if (app()->getLocale() == 'en') {
                    $subject = "تم تغيير كلمة المرور الخاصة بالبريد الإلكتروني -رأس الكيف";
                    $message = "<p>
We would like to inform you that your email password has been changed by the administration.
The new password is: " . $data['password'] . "
</p>
<p>
Please log in using the new password and update it immediately with a personal password to ensure account security.
<br />
For any inquiries or assistance, please contact the technical support department.
</p>";
                } else {
                    $subject = "The email password has been changed RKaayf";
                    $message = "<p>
نود إعلامكم بأنه قد تم تغيير كلمة المرور الخاصة بالبريد الإلكتروني الخاص بكم من قبل الإدارة.
كلمة المرور الجديدة هي: " . $data['password'] . "
</p>
<p>نرجو منكم تسجيل الدخول باستخدام كلمة المرور الجديدة وتحديثها بكلمة مرور خاصة بكم فوراً لضمان حماية الحساب.
<br />
لأي استفسار أو مساعدة، يُرجى التواصل مع قسم الدعم الفني.</p>";
                }
                //send email
                //\Mail::to($user->email)->send(new \App\Admin\Domain\Mail\ForgetPasswordMail($message));
                sendMail($subject, $user->email, $message);
            }

            $user->update(['password' => Hash::make($data['password'])]);

            return new GenericPayload(['message' => __('success.sentSuccessfully')], Response::HTTP_OK);

        } catch (\Exception $ex) {
            info($ex->getMessage());
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
