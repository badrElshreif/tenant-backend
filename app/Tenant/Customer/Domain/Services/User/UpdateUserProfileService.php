<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Arr;
use App\User\Domain\Models\Customer;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserProfileService extends Service
{
    public function handle($data = [])
    {
        //upload image
        $user = auth()->user();
        // if(isset($data['avatar'])) {
        //     if($user->avatar)
        //         $this->deleteFile($user->avatar, 'user/avatar');
        // }


        if(isset($data['phone']) || isset($data['country_code'])){

            $data['phone'] = isset($data['phone']) ? $data['phone'] : $user->phone;
            $data['country_code'] = isset($data['country_code']) ? $data['country_code'] : $user->country_code;

            if(str_starts_with($data['phone'], '0')){
                $data['phone'] = substr($data['phone'],1,20);
            }

            if($data['phone'] != $user->phone){
                $check_phone = Customer::wherePhone($data['phone'])->whereCountryCode($data['country_code'])->where('id', '!=', $user->id)->first();
                if($check_phone){
                    return new GenericPayload(
                         __('error.phoneExist'), 422
                    );
                }
                $check_user_phone = Customer::whereCountryCode($data['country_code'])->wherePhone($data['phone'])->where('phone', '!=', $user->phone)->first();
                if($check_user_phone)
                    return new GenericPayload( __('error.duplicatePhone'), 422
                        );

                if($user->phone != $data['phone']){
                    $data["updated_phone"] = $data['phone'];
                    $data["phone_updated_at"] = null;
                    $data['verification_code'] = rand(1111, 9999);
                    $message = __('general.sms.phoneVerificationCode').$data['verification_code'];
                    sendSMS($data['country_code'].$data['phone'], $message);
                }
            }

        }

        $user->update(Arr::only($data, ['name', 'email', 'avatar', 'country_code', 'gender', 'phone_updated_at', 'verification_code', 'updated_phone']));
        return new GenericPayload($user, Response::HTTP_CREATED);
    }
}
