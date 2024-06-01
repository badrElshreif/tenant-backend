<?php

namespace App\Tenant\Admin\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends CustomApiRequest
{
    public function rules()
    {
        switch ($this->method()){
            case 'GET':
                return [
                    'orderBy' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['id', 'name', 'phone', 'created_at', 'email', 'is_active']),
                    ],
                    'orderType' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                    ],
                    'is_paginated' => ['sometimes', 'nullable']
                ];
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'name' => ['required', 'min:4', 'max:40', 'string'],
                    'phone' => ['sometimes', 'digits_between:7,13', 'unique:admins,phone'],
                    'email' => ['required', 'email','unique:admins,email'],
                    'password' => ['required', 'string', 'min:8', 'max:32', 'confirmed'],
                    'avatar'   => ['nullable', 'nullable', 'url'],
                    'roles' => ['nullable', 'array'],
                    'roles.*'=>['nullable', 'numeric', 'exists:roles,id'],
                    'is_active' => ['nullable', 'boolean']
                ];
            case 'PUT':
            case 'PATCH':
                //$id = $this->route('id');
                $id = isset($this->id) ? $this->id : auth()->id();
                $rules = [
                    'name' => ['sometimes', 'min:4', 'max:40', 'string'],
                    //'phone' => ['sometimes', 'digits_between:7,13', 'unique:admins,phone,'.$id],
                    'phone' => ['sometimes', 'digits_between:7,17'],
                    'email' => ['sometimes', 'email' ,'unique:admins,email,'.$id],
                    'avatar'=>['sometimes', 'url'],
                    'image'=>['nullable','sometimes', 'url'],
                    'en.name' => [
                        'sometimes',
                        'max:255',
                         // Rule::unique('store_translations', 'name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'en');
                         //     })
                    ],
                    'ar.name' => [
                        'sometimes',
                        'max:255',
                         // Rule::unique('store_translations', 'name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'ar');
                         //     })
                    ],

                    'is_active' => ['sometimes', 'boolean'],
                    'password' => ['sometimes', 'string', 'min:8', 'max:32', 'confirmed'],
                ];
                if(isset($this->id)){
                    $rules = array_merge($rules, [
                        'roles' => ['required', 'array'],
                        'roles.*'=>['sometimes', 'numeric', 'exists:roles,id']                    ]);
                }
                return $rules;
        }
    }
}
