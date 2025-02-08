<?php

namespace App\User\Domain\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Infrastructure\Http\Requests\API\CustomApiRequest;
/**
 * Class VerifyAccountFormRequest
 * @package App\Customer\Domain\Requests
 */
class UpdatePhoneFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'country_code' => ['required', 'exists:countries,code'],
            'phone' => ['required', 'digits_between:7,17'],
            "code" => ['required', 'numeric'],
        ];
    }
}
