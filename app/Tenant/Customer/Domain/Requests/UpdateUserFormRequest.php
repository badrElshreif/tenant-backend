<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class UpdateUserFormRequest extends CustomApiRequest
{
    public function rules()
    {
        $id = $this->route('id');
        return [
            'is_active' => ['nullable', 'boolean'],
            'name' => ['nullable', 'min:4', 'max:40', 'string'],
            //'phone' => ['nullable', 'digits_between:7,17', 'unique:users,phone,'.$id],
            'phone' => ['nullable', 'digits_between:7,17'],
            'email' => ['nullable', 'email', 'unique:users,email,'.$id],
            'avatar'=>['nullable', 'url'],
            'country_code' => ['nullable', 'exists:countries,code'],
        ];
    }
}
