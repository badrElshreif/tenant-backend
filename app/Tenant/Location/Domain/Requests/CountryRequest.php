<?php

namespace App\Tenant\Location\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends CustomApiRequest
{
    public function rules()
    {
        switch ($this->method()){
            case 'GET':
                return [
                    'orderBy' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['id', 'name', 'created_at', 'is_active', 'order']),
                    ],
                    'orderType' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                    ],
                    'is_paginated' => ['sometimes', 'nullable','in:1,0,true,false'],
                    'active' => ['sometimes', 'nullable','in:1,0,true,false'],
                    'is_detailed' => ['sometimes', 'nullable','in:1,0,true,false'],
                    'all' => ['nullable','in:1,0,true,false'],
                    'per_page'  => ['sometimes', 'nullable', 'numeric', 'gte:1']
                ];
            case 'DELETE':
                return [];

            case 'POST':
                return [
                   // 'flag' => ['required', 'url'],
                    'code' => ['required', 'string', 'min:2', 'max:50', 'unique:countries,code'],
                    'en.name' => [
                        'required',
                        'max:255',
                         // Rule::unique('country_translations', 'name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'en');
                         //     })
                    ],
                    'ar.name' => [
                        'required',
                        'max:255',
                         // Rule::unique('country_translations', 'name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'ar');
                         //     })
                    ],
                ];
            case 'PUT':
            case 'PATCH':

                $id = $this->route('id');
                return [
                    //'name' => ['required', 'string', 'min:3', 'max:50'],
                    'flag' => ['sometimes', 'url'],
                    'code' => ['sometimes', 'string', 'min:2', 'max:50', 'unique:countries,code,'.$this->id],
                    'en.name' => [
                        'sometimes',
                        'max:255',
                         // Rule::unique('country_translations', 'name')
                         //    ->where(function ($query) {
                         //        $query->where('locale', 'en')->where('country_id','!=',$this->id);
                         //    })
                    ],
                    'ar.name' => [
                        'sometimes',
                        'max:255',
                         // Rule::unique('country_translations', 'name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'ar')->where('country_id','!=',$this->id);
                         //     })
                    ],
                    'is_active' => ['nullable', 'boolean']
                ];

        }
    }
}
