<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class CreateUserService extends Service
{
    protected $user;

    public function __construct(Customer $user)
    {
        $this->user = $user;
    }

    public function handle($data = [])
    {
        if(str_starts_with($data['phone'], '0')){
            $data['phone'] = substr($data['phone'],1,20);
        }

        $data['password'] = Hash::make($data['password']);
        $data["phone_verified_at"] = now();
        $data['is_active'] = 1;
        $data["verification_code"] = null;
        if(isset($data['phone_key']))
            $data['phone'] = $data['phone_key'].$data['phone'];
        $check_user_phone = Customer::wherePhone($data['phone'])->first();
        if($check_user_phone)
            return new GenericPayload( __('error.duplicatePhone'), 422
                );
        $user = $this->user->create($data);
        //$user = $this->user->create(Arr::only($data, ["name", "email", "password", "is_active"]));
        return new GenericPayload($user, Response::HTTP_CREATED);
    }
}
