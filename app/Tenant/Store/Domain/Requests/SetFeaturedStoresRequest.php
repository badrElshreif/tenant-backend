<?php

namespace App\Tenant\Store\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class SetFeaturedStoresRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'stores' => ['array'],
            'stores.*' => ['numeric', 'exists:stores,id'],
            'type' => ['required', 'in:centers,stores']
        ];
    }
}
