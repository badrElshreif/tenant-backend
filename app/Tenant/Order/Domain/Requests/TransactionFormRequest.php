<?php

namespace App\Tenant\Order\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;
class TransactionFormRequest extends CustomApiRequest
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
                        'type' => ['required', 'in:pay_in,pay_out'],
                        'amount' => ['required', 'regex:/^\d*(\.\d{2})?$/'],
                        'en.reason' => ['required','max:255', 'min:1'],
                        'ar.reason' => ['required','max:255', 'min:1'],                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [];
                }
                default:break;

                }
    }
}
