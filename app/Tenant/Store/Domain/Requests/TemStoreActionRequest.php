<?php

namespace App\Tenant\Store\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class TemStoreActionRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'status' => ['required', 'in:accept,reject'],
            'store_id' => ['required', 'exists:stores,id']
        ];
    }
}
