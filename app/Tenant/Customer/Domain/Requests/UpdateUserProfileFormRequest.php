<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class UpdateUserProfileFormRequest extends CustomApiRequest
{
    public function rules()
    {
        $id = optional(auth()->user())->id;
        return [
            'name' => ['sometimes', 'min:4', 'max:40', 'string'],
            //'phone' => ['sometimes', 'digits_between:7,17', 'unique:users,phone,'.$id],
            'phone' => ['sometimes', 'digits_between:7,17'],
            'email' => ['sometimes', 'nullable', 'email', 'unique:users,email,'.$id],
            'avatar'=>['sometimes', 'nullable', 'url'],
            'is_active' => ['sometimes', 'numeric'],
            'country_code' => ['nullable', 'exists:countries,code'],
            'latitude' => ['sometimes', 'string', 'max:255'],
            'longitude' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
