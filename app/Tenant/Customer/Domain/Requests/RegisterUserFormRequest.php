<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class RegisterUserFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'min:4', 'max:40', 'string'],
            //'phone' => ['required', 'digits_between:7,17', 'unique:users,phone'],
            'phone' => ['required', 'digits_between:7,17'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:32', 'confirmed'],
            'gender' => ['sometimes', 'required', 'in:male,female'],
            'country_code' => ['required', 'exists:countries,code'],
            'latitude' => ['sometimes', 'nullable', 'string', 'max:255'],
            'longitude' => ['sometimes', 'nullable', 'string', 'max:255'],
            'confirmation' => ['required', 'in:1'],
        ];
    }
}
