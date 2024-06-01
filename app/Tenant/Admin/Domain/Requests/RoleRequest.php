<?php

namespace App\Tenant\Admin\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends CustomApiRequest
{
    public function rules()
    {
        $guard_name ='admin';
        if(auth()->guard('store')->check() ) {
            $guard_name ='store';
        }
        if(auth()->guard('center')->check()){
            $guard_name ='center';
        }
        switch ($this->method()){
            case 'GET':
                return [
                    'orderBy' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['id', 'name', 'display_name', 'created_at', 'is_active']),
                    ],
                    'orderType' => [
                        'sometimes',
                        'nullable',
                        Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                    ],
                    'is_paginated' => ['nullable','in:1,0,true,false'],
                    'active' => ['nullable','in:1,0,true,false'],
                    'per_page'  => ['nullable', 'numeric', 'gte:1'],
                ];
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'en.display_name' => [
                        'required',
                        'max:255',
                        // Rule::unique('role_translations', 'display_name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'en');
                        //     })

                    ],
                    'ar.display_name' => [
                        'required',
                        'max:255',
                        // Rule::unique('role_translations', 'display_name')
                        //     ->where(function ($query) {
                        //         $query->where('locale', 'ar');
                        //     })
                    ],
                    'permissions' => ['required', 'array'],
                    'permissions.*'=>['required', 'numeric', 'exists:permissions,id'],
                    'is_active' => ['nullable', 'boolean'],
                    'store_id' => ['nullable', 'numeric', 'exists:stores,id'],
                ];
            case 'PUT':
            case 'PATCH':

                $id = $this->route('id');
                return [
                    'en.display_name' => [
                        'sometimes',
                        'max:255',
                         // Rule::unique('role_translations', 'display_name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'en')->where('role_id','!=',$this->id);
                         //     })
                    ],
                    'ar.display_name' => [
                        'sometimes',
                        'max:255',
                         // Rule::unique('role_translations', 'display_name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'ar')->where('role_id','!=',$this->id);
                         //     })
                    ],
                    'is_active' => ['nullable', 'boolean'],
                    'permissions' => ['required', 'array'],
                    'permissions.*'=>['required', 'numeric', 'exists:permissions,id'],
                    //'store_id' => ['nullable', 'numeric', 'exists:stores,id'],
                ];
        }
    }
}
