<?php

namespace App\Tenant\Location\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class StateRequest extends CustomApiRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
                return [
                    'country_id' => [
                        'nullable', 'numeric',
                        Rule::exists('countries', 'id')->whereNull('deleted_at')
                    ],
                    'order_by' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['id', 'name', 'created_at', 'is_active']),
                    ],
                    'order_type' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                    ],
                    'is_paginated' => ['nullable', 'in:1,0,true,false'],
                    'active' => ['sometimes', 'nullable', 'in:1,0,true,false'],
                    'is_detailed' => ['sometimes', 'nullable', 'in:1,0,true,false'],
                    'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                    'cities' => ['nullable', 'in:1,0,true,false'],
                ];
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'en.name' => [
                        'required',
                        'max:255',
                        // Rule::unique('state_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'en');
                        //     })
                    ],
                    'ar.name' => [
                        'required',
                        'max:255',
                        // Rule::unique('state_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'ar');
                        //     })
                    ],
                    'country_id' => [
                        'required', 'numeric',
                        Rule::exists('countries', 'id')->whereNull('deleted_at')
                    ]
                ];
            case 'PUT':
            case 'PATCH':
                $id = $this->route('id');
                return [
                    'country_id' => [
                        'nullable', 'numeric',
                        Rule::exists('countries', 'id')->whereNull('deleted_at')
                    ],
                    'en.name' => [
                        'nullable',
                        'max:255',
                        // Rule::unique('state_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'en')->where('state_id','!=',$this->id);
                        //     })
                    ],
                    'ar.name' => [
                        'nullable',
                        'max:255',
                        // Rule::unique('state_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'ar')->where('state_id','!=',$this->id);
                        //     })
                    ],
                    'is_active' => ['nullable', 'boolean']
                ];
        }
    }
}
