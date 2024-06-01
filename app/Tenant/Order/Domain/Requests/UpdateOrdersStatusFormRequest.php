<?php

namespace App\Tenant\Order\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class UpdateOrdersStatusFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            //'status_id' => ['required', 'numeric', 'exists:statuses,id'],
            'orders'  => ['required', 'array'],
            'orders.*' => ['required', 'numeric', 'exists:orders,id'],
            'status' => ['required', 'exists:statuses,key'],
            'reason' => [
                Rule::requiredIf(function () {
                    return isset(request()->status) && (request()->status =='canceled' || request()->status =='rejected');
                }),
                'nullable',
                // 'string',
                //'min:2',
                'max:1000'
            ],
        ];
    }
}
