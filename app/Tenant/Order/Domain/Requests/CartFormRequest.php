<?php

namespace App\Tenant\Order\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;
class CartFormRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                        'device_id' => [
                            Rule::requiredIf(function () {
                                return !auth()->check() ;
                            }),
                            'string',
                            'max:255',
                            'min:1'
                        ],
                    ];
                }
                case 'DELETE': {
                    return [
                        'id' => ['required', 'numeric', 'exists:carts,id'],
                        'device_id' => [
                            Rule::requiredIf(function () {
                                return !auth()->check() ;
                            }),
                            'string',
                            'max:255',
                            'min:1'
                        ],
                    ];
                }
                case 'POST': {
                    return [
                        // 'items'  => ['sometimes', 'nullable', 'array'],
                        // 'items.*.product_id'  => ['numeric', 'exists:products,id'],
                        // 'items.*.warranty_id'  => ['numeric', 'exists:warranties,id'],
                        // 'items.*.quantity'  => ['numeric', 'gte:1'],
                        'product_id' => [
                            'required',
                            'numeric',
                            'exists:products,id'
                        ],
                        'warranty_id' => [
                            'nullable',
                            'numeric',
                            'exists:warranties,id'
                        ],
                        'quantity' => [
                            'required',
                            'numeric',
                            'gte:1'
                        ],
                        'device_id' => [
                            Rule::requiredIf(function () {
                                return !auth()->check() ;
                            }),
                            'string',
                            'max:255',
                            'min:1'
                        ],
                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [
                        'id' => ['required', 'numeric', 'exists:carts,id'],
                        'quantity' => ['required', 'numeric', 'gte:1'],
                        'warranty_id' => [
                            'nullable',
                            'numeric',
                            'exists:warranties,id'
                        ],
                        'device_id' => [
                            Rule::requiredIf(function () {
                                return !auth()->check() ;
                            }),
                            'string',
                            'max:255',
                            'min:1'
                        ],
                    ];
                }
                default:break;

                }
    }
}
