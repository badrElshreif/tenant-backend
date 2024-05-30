<?php

namespace  App\Infrastructure\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomApiRequest extends FormRequest {

// handle response in case of validation failed
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error'=>$validator->errors()->first(),
            ], 422)
        );
    }

}
