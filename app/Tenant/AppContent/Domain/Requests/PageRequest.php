<?php

namespace App\Tenant\AppContent\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class PageRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'en.title' => ['nullable', 'max:225'],
            'ar.title' => ['nullable', 'max:225'],
            'en.body' => ['nullable', 'max:1000'],
            'ar.body' => ['nullable', 'max:1000'],
        ];
    }
}
