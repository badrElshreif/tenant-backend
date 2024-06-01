<?php

namespace App\Tenant\Product\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class RatingFormRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                        'orderBy' => [
                            'nullable',
                            Rule::in(['id', 'rate', 'created_at', 'is_active', 'comment']),
                        ],
                        'orderType' => [
                            'nullable',
                            Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                        ],
                    ];
                }
                case 'DELETE': {
                    return [
                        'product_id' => ['required', 'numeric', 'exists:products,id'],
                    ];
                }
                case 'POST': {
                    return [
                        'product_id' => ['required', 'numeric', 'exists:products,id'],
                        'comment' => ['sometimes', 'nullable', 'string', 'max:255'],
                        'rate' => ['required', 'numeric', 'lte:5'],
                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [
                        'product_id' => ['required', 'numeric', 'exists:products,id'],
                        'comment' => ['sometimes', 'nullable', 'string', 'max:255'],
                        'rate' => ['required', 'numeric', 'lte:5'],
                    ];
                }
                default:break;

                }
    }
}
