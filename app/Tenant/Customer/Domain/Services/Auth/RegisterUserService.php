<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserService extends Service
{
    protected $user;

    public function __construct(Customer $user)
    {
        $this->user = $user;
    }

    public function handle($data = [])
    {
        $data['password'] = Hash::make($data['password']);
        $data['verification_code'] = rand(1111, 9999);
        if(str_starts_with($data['phone'], '0')){
            $data['phone'] = substr($data['phone'],1,20);
        }

        $check_user_phone = Customer::wherePhone($data['phone'])->whereCountryCode($data['country_code'])->first();

        if($check_user_phone)
            return new GenericPayload( __('error.duplicatePhone'), 422);

        $user = $this->user->create($data);
        //$message = __('general.sms.phoneVerificationCode').$data['verification_code'];
        //abanoub edit
                if($data['country_code'] = "SA"){
            $data['country_code'] = "966";
            if(str_starts_with($data['phone'], '966')){
                $data['phone'] = substr($data['phone'],3);
            }
        }
        //abanoub edit
       $userSms = send_msegat_sms($data['country_code'].$data['phone'], $data['verification_code']);
        //$user = $this->user->create(Arr::only($data, ["name", "email", "password", "is_active"]));
        return new GenericPayload(
            [
                'message' => __('success.successfullyRegistered'),
                //'code'    => $user->verification_code,
                'userSms' => $userSms ,
            ],
            Response::HTTP_RESET_CONTENT
        );
    }
}
