<?php

namespace App\Tenant\Product\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class FavouriteFormRequest extends CustomApiRequest
{
    public function rules()
    {
        $rules= [
            'per_page'  => ['sometimes', 'nullable', 'numeric', 'gte:1']
        ];

        if(!request()->isMethod('get'))
        	$rules['product_id'] = ['required', 'numeric', 'exists:products,id'];

        return $rules;
    }
}
