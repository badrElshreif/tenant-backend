<?php

namespace App\User\Domain\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Infrastructure\Http\Requests\API\CustomApiRequest;
/**
 * Class VerifyAccountFormRequest
 * @package App\Customer\Domain\Requests
 */
class VerifyAccountFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'phone' => ['required', 'digits_between:7,17'],
            'country_code' => ['required', 'exists:countries,code'],
            "code" => ['required', 'numeric'],
            "device_token" => "sometimes|nullable|string|max:1000|min:1",
            "device_id" => "sometimes|nullable|string|max:255|min:1",
        ];
    }

    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    // }
}
