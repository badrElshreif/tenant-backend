<?php

namespace App\Tenant\Product\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends CustomApiRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            {
                return [
                    'orderBy' => [
                        'nullable',
                        Rule::in(['id', 'name', 'created_at', 'price', 'most_selling', 'most_rated', 'preview_fees', 'is_active']),
                    ],
                    'orderType' => [
                        'nullable',
                        Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                    ],
                    'is_paginated' => ['nullable'],
                    'active' => ['nullable'],
                    'price_from' => ['nullable'],
                    'price_to' => ['nullable'],
                    'price' => ['nullable'],
                    'is_detailed' => ['nullable'],
                    'per_page' => ['nullable', 'numeric', 'gte:1'],
                    'type' => ['nullable', 'in:stores,centers'],
                    'has_pagination' => ['nullable'],
                    'country_id' => ['nullable'],
                    'state_id' => ['nullable'],
                    'city_id' => ['nullable'],
                ];
            }
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                $category = \App\Category\Domain\Models\Category::find($this->category_id);
                return [
                    'en.name' => [
                        'required',
                        'max:255',
                        // Rule::unique('product_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'en');
                        //     })
                    ],
                    'ar.name' => [
                        'required',
                        'max:255',
                        // Rule::unique('product_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'ar');
                        //     })
                    ],
                    'en.description' => ['required', 'max:1000'],
                    'ar.description' => ['required', 'max:1000'],
                    'category_id' => ['required', 'numeric', 'exists:categories,id'],
                    'brand_id' => [
                        Rule::requiredIf(function () use ($category) {
                            return $category && $category->type == 'stores';
                        }),
                        'numeric',
                        'exists:brands,id'
                    ],
                    'made_in' => [
                        Rule::requiredIf(function () use ($category) {
                            return $category && $category->type == 'stores';
                        }),
                        'numeric',
                        'exists:countries,id'
                    ],
                    'store_id' => [
                        Rule::requiredIf(function () use ($category) {
                            return !auth('store')->check() || auth('store')->user()->store == null;
                        }),
                        'numeric',
                        'exists:stores,id'
                    ],
                    'barcode' => [
                        // Rule::requiredIf(function () use ($category){
                        //     return $category && $category->type == 'stores';
                        // }),
                        'nullable',
                        'unique:products,barcode',
                        'string', 'min:1', 'max:255'
                    ],
                    'price' => ['required', 'regex:/^\d*(\.\d{3})?$/'],
                    'quantity' => [
                        Rule::requiredIf(function () use ($category) {
                            return $category && $category->type == 'stores';
                        }),
                        'numeric'
                    ],
                    'preview_fees' => [
                        Rule::requiredIf(function () use ($category) {
                            return $category && $category->type == 'centers';
                        }),
                        'regex:/^\d*(\.\d{3})?$/'
                    ],
                    'max_purchase_quantity' => [
                        Rule::requiredIf(function () use ($category) {
                            return $category && $category->type == 'stores';
                        }),
                        'numeric', 'lte:quantity'
                    ],
                    'is_active' => ['nullable', 'boolean'],
                    'ar.tags' => ['nullable', 'array'],
                    'en.tags' => ['nullable', 'array'],
                    'image' => ['required', 'url'],
                    'catalog' => ['nullable', 'url'],
                    'attachments' => ['nullable', 'array'],
                    'attachments.*.name' => [
                        'nullable', 'string', 'min:1', 'max:255',
                        Rule::requiredIf(function () use ($category) {
                            return isset(request()->attachments);
                        })
                    ],
                    'attachments.*.type' => [
                        'nullable', 'string', 'in:image,pdf,video',
                        Rule::requiredIf(function () use ($category) {
                            return isset(request()->attachments);
                        })
                    ],
                    'attachments.*.folder' => [
                        'nullable', 'string', 'min:1', 'max:255',
                        Rule::requiredIf(function () use ($category) {
                            return isset(request()->attachments);
                        })
                    ],
                    'attachments.*.description' => [
                        'nullable', 'string', 'min:1', 'max:255',
                        // Rule::requiredIf(function () use ($category){
                        //     return isset(request()->attachments);
                        // })
                    ],
                    'properties' => ['nullable', 'array'],
                    'properties.*' => [
                        'array',
                        Rule::requiredIf(function () use ($category) {
                            return isset(request()->properties);
                        }),
                    ],
                    'properties.*.property_id' => [
                        'numeric',
                        'exists:properties,id',
                        // Rule::requiredIf(function () use ($category){
                        //     return isset(request()->properties);
                        // }),
                    ],
                    'properties.*.value' => [
                        'nullable',
                        // Rule::requiredIf(function () use ($category){
                        //     return isset(request()->properties);
                        // }),
                    ],
                    //'properties.*.property_option_id' => ['required', 'nullable', 'numeric', 'exists:property_options,id'],
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'en.name' => [
                        'nullable',
                        'max:255',
                        // Rule::unique('product_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'en')->where('product_id','!=',$this->id);
                        //     })
                    ],
                    'ar.name' => [
                        'nullable',
                        'max:255',
                        // Rule::unique('product_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'ar')->where('product_id','!=',$this->id);
                        //     })
                    ],
                    'en.description' => ['nullable', 'max:1000'],
                    'ar.description' => ['nullable', 'max:1000'],
                    'category_id' => ['nullable', 'numeric', 'exists:categories,id'],
                    'brand_id' => ['nullable', 'numeric', 'exists:brands,id'],
                    'store_id' => ['nullable', 'numeric', 'exists:stores,id'],
                    'made_in' => ['nullable', 'numeric', 'exists:countries,id'],
                    'barcode' => ['nullable', 'string', 'min:1', 'max:255', 'unique:products,barcode,' . $this->id],
                    'price' => ['nullable', 'regex:/^\d*(\.\d{3})?$/'],
                    'quantity' => ['nullable', 'numeric'],
                    'preview_fees' => ['nullable', 'regex:/^\d*(\.\d{3})?$/'],
                    'max_purchase_quantity' => ['nullable', 'numeric', 'lte:quantity'],
                    'is_active' => ['nullable', 'boolean'],
                    'deactivation_start_date' => ['nullable', 'date'],
                    'deactivation_end_date' => ['nullable', 'date', 'after_or_equal:deactivation_start_date'],
                    'ar.tags' => ['nullable', 'array'],
                    'en.tags' => ['nullable', 'array'],
                    'image' => ['nullable', 'url'],
                    'catalog' => ['nullable', 'url'],
                    'attachments' => ['nullable', 'array'],
                    'properties' => ['nullable', 'array'],
                    'properties.*' => ['nullable', 'array'],
                    'properties.*.property_id' => ['nullable', 'exists:properties,id'],
                    'properties.*.value' => ['nullable'],
                    // 'properties.*.property_option_id.*' => ['nullable', 'numeric', 'exists:property_options,id'],
                ];
            }
            default:
                break;

        }
    }
}
