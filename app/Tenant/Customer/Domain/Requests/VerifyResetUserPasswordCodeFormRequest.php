<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class VerifyResetUserPasswordCodeFormRequest extends CustomApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'type' => 'sometimes|nullable|in:phone,email',
            'token' => 'required',
        ];
        if ((isset($this->type) && $this->type == 'email') || !empty($this->email)) {
            $rules['email'] = ['required', 'email'];
        } else {
            $rules['phone'] = ['required', 'digits_between:7,17'];
            $rules['country_code'] = ['required', 'exists:countries,code'];
        }

        return $rules;
    }
}
