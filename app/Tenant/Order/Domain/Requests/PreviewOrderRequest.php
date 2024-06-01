<?php

namespace App\Tenant\Order\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;
class PreviewOrderRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            //'store_id' => ['required', 'numeric', 'exists:stores,id'],
            'type' => ['required', 'in:stores,centers'],
            'use_wallet' => ['nullable', 'boolean'],
            'service_id' => [
                //'required',
                Rule::requiredIf(function () {
                    return request()->type == 'centers' ;
                }),
                'numeric', 'exists:products,id'],
            'promo_code' => ['nullable', 'string', 'min:1', 'max:255', 'exists:promo_codes,code'],
            // 'items' => ['required', 'array'],
            // 'items.*.product_id' => ['required', 'numeric', 'exists:products,id'],
            // 'items.*.warranty_id' => ['nullable', 'numeric', 'exists:warranties,id'],
            // 'items.*.quantity' => ['required', 'numeric', 'gte:1'],

        ];
    }
}
