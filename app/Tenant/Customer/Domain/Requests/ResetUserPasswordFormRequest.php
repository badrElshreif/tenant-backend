<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class ResetUserPasswordFormRequest extends CustomApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => 'nullable|in:phone,email',
            'password' => ['required', 'min:6', 'max:32', 'confirmed'],
            "token" => ['required']
        ];
        if ($this->type == 'email') {
            $rules['email'] = ['required', 'email'];
        } else {
            $rules['phone'] = ['required', 'digits_between:7,17'];
            $rules['country_code'] = ['required', 'exists:countries,code'];

        }
        return $rules;
    }
}
