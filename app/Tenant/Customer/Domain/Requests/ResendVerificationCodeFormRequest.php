<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class ResendVerificationCodeFormRequest extends CustomApiRequest
{
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