<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class ForgetUserPasswordFormRequest extends CustomApiRequest
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
            ];
        if(request()->type == 'email'){
             $rules['email'] = ['required', 'email'];
        }else{
             $rules['phone'] = ['required', 'digits_between:7,17'];
              $rules['country_code'] =['required', 'exists:countries,code'];
        
        }
        
        return $rules;
       
    }
}
