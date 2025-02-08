<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class NotificationFormRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                        'page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                        'is_read' =>['nullable', 'boolean']
                    ];
                }
                case 'DELETE': 
                case 'POST': {
                    return [];
                }
                case 'PUT':
                case 'PATCH': {
                    return [];
                }
                default:break;
    
                }
    }
}
