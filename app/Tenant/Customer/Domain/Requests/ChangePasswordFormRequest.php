<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class ChangePasswordFormRequest extends CustomApiRequest
{
    public function rules()
    {

        return [
            'old_password' => 'required|string|min:8|max:32',
            'new_password' => 'required|string|min:8|max:32|confirmed',
        ];
    }
}
