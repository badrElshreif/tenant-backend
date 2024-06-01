<?php

namespace App\Tenant\Order\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class PaymentRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                    ];
                }
                case 'DELETE': {
                    return [];
                }
                case 'POST': {
                    return [
                        'orders' => ['required', 'array'],
                        'orders.*' => ['required', 'numeric', 'exists:orders,id'],
                        'receipt' => ['required', 'url'],
                        'amount' => ['nullable', 'regex:/^\d*(\.\d{3})?$/']
                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [];
                }
                default:break;

                }
    }
}
