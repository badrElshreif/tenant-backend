<?php

namespace App\Tenant\Admin\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class ChangePasswordFormRequest extends CustomApiRequest
{
    public function rules()
    {

        return [
            'old_password' => 'required|string|min:6|max:32',
            'password' => 'required|string|min:6|max:32|confirmed',
        ];
    }
}
