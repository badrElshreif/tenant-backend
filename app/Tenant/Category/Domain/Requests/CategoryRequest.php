<?php

namespace App\Tenant\Category\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;
class CategoryRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'orderBy' => [
                            'sometimes',
                            'nullable',
                            Rule::in(['id', 'name', 'created_at', 'is_active']),
                        ],
                        'orderType' => [
                            'sometimes',
                            'nullable',
                            Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                        ],
                        'full' => ['nullable'],
                        'is_paginated' => ['nullable'],
                        'active' => ['nullable'],
                        'is_detailed' => ['nullable'],
                        'all' => ['nullable'],
                        'per_page'  => ['nullable', 'numeric', 'gte:1'],
                        'type' => ['nullable', 'in:stores,centers'],
                        'category_id' => ['nullable', 'numeric', 'exists:categories,id']
                    ];
                }
                case 'DELETE': {
                    return [];
                }
                case 'POST': {
                    return [
                        'en.name' => [
                            'nullable',
                            'max:255',
                             // Rule::unique('category_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'en');
                             //     })
                        ],
                        'ar.name' => [
                            'required',
                            'max:255',
                             // Rule::unique('category_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'ar');
                             //     })
                        ],
                        'en.description' => ['nullable', 'max:1000'],
                        'ar.description' => ['nullable', 'max:1000'],
                        'parent_id' => ['nullable', 'numeric', 'exists:categories,id'],
                        'order' => ['sometimes','nullable', 'numeric', 'min:1', 'max:10', 'lte:10', 'gte:1'],
                        'image' => ['nullable', 'url'],
                        'is_active' => ['nullable', 'boolean'],
                        'tax_percentage' => ['nullable', 'digits_between:1,100', 'lte:100', 'gt:0'],
                      //  'type' => ['required', 'in:stores,centers']
                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [
                        'en.name' => [
                            'nullable',
                            'max:255',
                             // Rule::unique('category_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'en')->where('category_id','!=',$this->id);
                             //     })
                        ],
                        'ar.name' => [
                            'nullable',
                            'max:255',
                             // Rule::unique('category_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'ar')->where('category_id','!=',$this->id);
                             //     })
                        ],
                        'en.description' => ['nullable', 'max:1000'],
                        'ar.description' => ['nullable', 'max:1000'],
                        'parent_id' => ['nullable', 'numeric', 'exists:categories,id'],
                        'image' => ['nullable', 'url'],
                        'order' => ['nullable', 'numeric', 'min:1', 'max:10', 'lte:10', 'gte:1'],
                        'is_active' => ['nullable', 'boolean'],
                        'tax_percentage' => ['nullable', 'digits_between:1,100', 'lte:100', 'gt:0'],
                        'type' => ['nullable', 'in:stores,centers']
                    ];
                }
                default:break;

                }
    }
}
