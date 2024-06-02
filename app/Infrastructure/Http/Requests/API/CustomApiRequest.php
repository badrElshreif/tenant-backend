<?php

namespace App\Infrastructure\Http\Requests\API;

use App\Infrastructure\Traits\RESTApi;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomApiRequest extends FormRequest
{
    use RESTApi;

// handle response in case of validation failed
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendError($validator->errors(), 422),
        );
    }

}
