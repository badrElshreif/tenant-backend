<?php

namespace App\Tenant\Offer\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'orderBy' => [
                            'nullable',
                            Rule::in(['id', 'type', 'name', 'created_at', 'price', 'most_selling', 'most_rated', 'preview_fees', 'is_active']),
                        ],
                        'orderType' => [
                            'nullable',
                            Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                        ],
                        'is_paginated' => ['sometimes', 'nullable'],
                        'active' => ['sometimes', 'nullable'],
                        'price_from' => ['nullable'],
                        'price_to' => ['nullable'],
                        'price' => ['nullable'],
                        'is_detailed' => ['sometimes', 'nullable'],
                        'per_page'  => ['sometimes', 'nullable', 'numeric', 'gte:1']
                    ];
                }
                case 'DELETE': {
                    return [];
                }
                case 'POST': {
                    $rules = [
                        'en.name' => [
                            'required',
                            'max:255',
                             // Rule::unique('offer_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'en');
                             //     })
                        ],
                        'ar.name' => [
                            'required',
                            'max:255',
                             // Rule::unique('offer_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'ar');
                             //     })
                        ],
                        'type' => ['required', 'in:free_product,percentage,value'],
                        // 'free_product_id' => [
                        //     'numeric',
                        //     'exists:products,id',
                        //     Rule::requiredIf(function () {
                        //         return isset(request()->type) && ! request()->type=='free_product';
                        //     }),
                        // ],
                        // 'value' => [
                        //     Rule::requiredIf(function () {
                        //         return isset(request()->type) && ! request()->type!='free_product';
                        //     }),
                        // ],
                        'is_active' => ['sometimes', 'boolean'],
                        'start_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                        'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                        'products' => ['required', 'array'],
                        'products.*' => ['required', 'exists:products,id'],
                        "free_product_id" => ['required_if:type,free_product', 'numeric', 'exists:products,id', 'nullable'],
                        //"value" => ["required_if:type,percentage,value", 'nullable']
                    ];
                    if (request()->type == "percentage")
                        $rules['value'] = ['required', 'numeric', 'lte:100', 'gt:0'];

                    if (request()->type == "value")
                        $rules['value'] = ['required', 'regex:/^\d*(\.\d{2})?$/'];
                    return $rules;
                }
                case 'PUT':
                case 'PATCH': {
                    $rules = [
                        'en.name' => [
                            'sometimes',
                            'max:255',
                             // Rule::unique('offer_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'en')->where('offer_id','!=',$this->id);
                             //     })
                        ],
                        'ar.name' => [
                            'sometimes',
                            'max:255',
                             // Rule::unique('offer_translations', 'name')
                             //     ->where(function ($query) {
                             //         $query->where('locale', 'ar')->where('offer_id','!=',$this->id);
                             //     })
                        ],
                        'type' => ['sometimes', 'in:free_product,percentage,value'],
                        // 'free_product_id' => [
                        //     'numeric',
                        //     'exists:products,id',
                        //     Rule::requiredIf(function () {
                        //         return isset(request()->type) && ! request()->type=='free_product';
                        //     }),
                        // ],
                        'value' => [
                            Rule::requiredIf(function () {
                                return isset(request()->type) && ! request()->type!='free_product';
                            }),
                        ],
                        'is_active' => ['sometimes', 'boolean'],
                        'start_date' => ['sometimes', 'date', 'date_format:Y-m-d'],
                        'end_date' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                        'products' => ['sometimes', 'array'],
                        'products.*' => ['sometimes', 'exists:products,id'],
                        "free_product_id" => ['nullable', 'required_if:type,free_product', 'numeric', 'exists:products,id'],
                        //"value" => "required_if:type,percentage,value",
                    ];
                    if (request()->type == "percentage") {
                        $rules['value'] = ['nullable', 'numeric', 'lte:100', 'gt:0'];
                    }

                    if (request()->type == "value") {
                        $rules['value'] = ['nullable', 'regex:/^\d*(\.\d{3})?$/'];
                    }
                    return $rules;
                }
                default:break;

                }
    }

    public function messages()
    {
        return [
            'start_date.after_or_equal'=>trans('validation.after_or_equal',['attribute'=>trans('validation.attributes.start_date'),'date'=>trans('validation.attributes.today')]),

        ];
    }
}
