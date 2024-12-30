<?php

namespace App\Tenant\Location\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class CityRequest extends CustomApiRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
                return [
                    'state_id' => [
                        'nullable', 'numeric',
                        Rule::exists('states', 'id')->whereNull('deleted_at')],
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
                    'is_paginated' => ['nullable', 'in:1,0,true,false'],
                    'active' => ['nullable', 'in:1,0,true,false'],
                    'is_detailed' => ['nullable', 'in:1,0,true,false'],
                    'per_page' => ['nullable', 'numeric', 'gte:1'],
                    'states' => ['nullable'],
                ];
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'en.name' => [
                        'required',
                        'max:255',
                        // Rule::unique('city_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'en');
                        //     })
                    ],
                    'ar.name' => [
                        'required',
                        'max:255',
                        // Rule::unique('city_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'ar');
                        //     })
                    ],
                    'state_id' => [
                        'required', 'numeric',
                        Rule::exists('states', 'id')->whereNull('deleted_at')
                        //'exists:states,id'
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
                    'en.name' => [
                        'sometimes',
                        'max:255',
                        // Rule::unique('city_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'en')->where('city_id','!=',$this->id);
                        //     })
                    ],
                    'ar.name' => [
                        'sometimes',
                        'max:255',
                        // Rule::unique('city_translations', 'name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'ar')->where('city_id','!=',$this->id);
                        //     })
                    ],
                    'is_active' => ['nullable', 'boolean'],
                    'state_id' => [
                        'nullable', 'numeric',
                        Rule::exists('states', 'id')->whereNull('deleted_at')
                    ],
                    'country_id' => [
                        'nullable', 'numeric',
                        Rule::exists('countries', 'id')->whereNull('deleted_at')
                    ]

                ];

        }
    }
}
