<?php

namespace App\Tenant\AppContent\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class ContactUsFormRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'per_page' => ['nullable', 'numeric', 'gte:1'],
                        'orderBy' => [
                            'nullable',
                            Rule::in(['id', 'name', 'created_at', 'email', 'phone', 'email']),
                        ],
                        'orderType' => [
                            'nullable',
                            Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                        ],
                    ];
                }
                case 'DELETE': {
                    return [];
                }
                case 'POST': {
                    return [
                        'title' => ['sometimes', 'string', 'max:1000', 'min:1'],
                        'name' => ['sometimes', 'string', 'max:1000', 'min:1'],
                        'email' => ['sometimes', 'email'],
                        'phone' => ['required', 'string', 'max:25', 'min:1'],
                        'body'  => ['required', 'string', 'max:1000', 'min:1'],
                        'contact_type_id' => ['nullable', 'numeric', 'exists:contact_types,id']
                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [
                        'title' => ['sometimes', 'string', 'max:1000', 'min:1'],
                        'name' => ['sometimes', 'string', 'max:1000', 'min:1'],
                        'email' => ['sometimes', 'email'],
                        'phone' => ['sometimes', 'string', 'max:25', 'min:1'],
                        'body'  => ['required', 'string', 'max:1000', 'min:1'],
                        //'parent_id' => ['required', 'numeric', 'exists:contact_us,id']
                    ];
                }
                default:break;

                }
    }
}
