<?php

namespace App\Tenant\Admin\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class ForgetPasswordRequest extends CustomApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'exists:admins,email', 'min:6', 'max:255'],
            'type' => ['nullable', 'in:stores,centers']
        ];
    }
}
