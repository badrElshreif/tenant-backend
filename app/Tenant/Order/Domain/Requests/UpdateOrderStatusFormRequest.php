<?php

namespace App\Tenant\Order\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'status' => ['required', 'exists:statuses,key'],
            //'status' => ['required', 'in:canceled,rejected,accepted,delivered'],
            'reason' => [
                Rule::requiredIf(function () {
                    return isset(request()->status) && (request()->status =='canceled' || request()->status =='rejected');
                }),
                // 'nullable',
                // 'string',
                //'min:2',
                'max:1000'
            ],
        ];
    }
}
