<?php

namespace App\User\Domain\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class LogoutUserFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            "device_token" => "sometimes|nullable|string|max:1000|min:1"
        ];
    }
}
