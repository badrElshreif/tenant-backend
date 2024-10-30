<?php

namespace App\Property\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class PropertyRequest extends CustomApiRequest
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
                        'is_paginated' => ['sometimes', 'nullable'],
                        'active' => ['sometimes', 'nullable'],
                        'is_detailed' => ['sometimes', 'nullable'],
                        'per_page'  => ['sometimes', 'nullable', 'numeric', 'gte:1']
                    ];
                }
                case 'DELETE': {
                    return [];
                }
                case 'POST': {
                    return [
                        'property_type_id' => ['required', 'numeric', 'exists:property_types,id'],
                        'en.name' => [
                            'required',
                            'max:255',
                            Rule::unique('property_translations', 'name')
                                ->where(function ($query) {
                                    $query->where('locale', 'en');
                                })
                        ],
                        'ar.name' => [
                            'required',
                            'max:255',
                            Rule::unique('property_translations', 'name')
                                ->where(function ($query) {
                                    $query->where('locale', 'ar');
                                })
                        ],
                        'is_active' => ['nullable', 'boolean'],
                        'is_general' => ['nullable', 'boolean'],
                        'is_required' => ['required', 'boolean'],
                        'categories' => ['required', 'array'],
                        'categories.*'=>['required', 'numeric', 'exists:categories,id'],
                        'options' => [
                            'array',
                            Rule::requiredIf(function () {
                                return isset(request()->has_options) && request()->has_options == 1;
                            }),
                        ],
                        'options.*.en.name' => [
                            'required',
                            'max:255',
                            Rule::unique('property_option_translations', 'name')
                                ->where(function ($query) {
                                    $query->where('locale', 'en');
                                })
                        ],
                        'options.*.ar.name' => [
                            'required',
                            'max:255',
                            Rule::unique('property_option_translations', 'name')
                                ->where(function ($query) {
                                    $query->where('locale', 'ar');
                                })
                        ]
                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [
                        'property_type_id' => ['required', 'numeric', 'exists:property_types,id'],
                        'en.name' => [
                            'sometimes',
                            'max:255',
                             Rule::unique('property_translations', 'name')
                                 ->where(function ($query) {
                                     $query->where('locale', 'en')->where('property_id','!=',$this->id);
                                 })
                        ],
                        'ar.name' => [
                            'sometimes',
                            'max:255',
                             Rule::unique('property_translations', 'name')
                                 ->where(function ($query) {
                                     $query->where('locale', 'ar')->where('property_id','!=',$this->id);
                                 })
                        ],
                        'is_active' => ['sometimes', 'boolean'],
                        'is_general' => ['sometimes', 'boolean'],
                        'is_required' => ['sometimes', 'boolean'],
                        'has_options' => ['sometimes', 'boolean'],
                        'categories' => ['required', 'array'],
                        'categories.*'=>['required', 'numeric', 'exists:categories,id'],
                        'options' => [
                            'array',
                            Rule::requiredIf(function () {
                                return isset(request()->has_options) && request()->has_options == 1;
                            }),
                        ],
                        'options.*.id' => ['sometimes', 'numeric', 'exists:property_options,id'],
                        'options.*.en.name' => [
                            'required',
                            'max:255',
                            // Rule::unique('property_option_translations', 'name')
                            //     ->where(function ($query) {
                            //         $query->where('locale', 'en')->where('property_option_id','!=','options.*.id');
                            //     })
                        ],
                        'options.*.ar.name' => [
                            'required',
                            'max:255',
                            // Rule::unique('property_option_translations', 'name')
                            //     ->where(function ($query) {
                            //         $query->where('locale', 'ar')->where('property_option_id','!=','options.*.id');
                            //     })
                        ]
                    ];
                }
                default:break;
    
                }
    }
}
