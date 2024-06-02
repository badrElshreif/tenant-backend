<?php

namespace App\Main\Tenant\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class CreateTenantFormRequest extends CustomApiRequest
{
    public function rules()
    {

        return [
            'name' => 'required|string|min:3|max:20',
            'email' => 'required|email|unique:users,email,id',
            'password' => 'required|min:8',
        ];
    }
}
