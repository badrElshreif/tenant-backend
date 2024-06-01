<?php

namespace App\Tenant\Store\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class ToggleStoreStatusRequest extends CustomApiRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'GET': {
                return [
                    'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                    'is_paginated' => ['nullable', 'boolean'],
                    'latitude' => ['nullable', 'string'],
                    'longitude' => ['nullable', 'string'],

                    'orderBy' => [
                        'nullable',
                        Rule::in(['id', 'name', 'email', 'is_active', 'phone', 'created_at', 'is_featured']),
                    ],
                    'orderType' => [
                        'nullable',
                        Rule::in(['ASC', 'DESC', 'asc', 'desc']),
                    ],
                ];
            }
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                $rules =  [
                    'en.name' => [
                        'required',
                        'max:255',
                          Rule::unique('store_translations', 'name')
                              ->where(function ($query) {
                                  $query->where('locale', 'en');
                              })
                    ],
                    'ar.name' => [
                        'required',
                        'max:255',
                          Rule::unique('store_translations', 'name')
                              ->where(function ($query) {
                                  $query->where('locale', 'ar');
                              })
                    ],
                    'password' => ['required', 'string', 'min:6', 'max:20'],
                    // 'is_featured' => ['nullable', 'boolean'],
                    'phone' => ['required', 'digits_between:7,17'],
                    'email' => ['required', 'email','unique:stores,email'],
                    'city_id' => ['required', 'numeric', 'exists:cities,id'],
                    'state_id' => ['required', 'numeric', 'exists:states,id'],
                    'country_id' => ['required', 'numeric', 'exists:countries,id'],
                    'latitude' => ['required', 'string', 'max:255'],
                    'longitude' => ['required', 'string', 'max:255'],
                    'address' => ['nullable', 'string', 'max:255'],
                    'username' => ['required', 'string', 'max:255'],
                    'bank_name' => ['nullable', 'string', 'max:255'],
                    'iban_no' => ['nullable', 'string', 'max:255'],
                    'swift_code' => ['nullable', 'string', 'max:255'],
                    'bank_account_no' => ['nullable', 'string', 'max:255'],
                    'commercial_registry_no' => ['required', 'string', 'max:255'],
                    'commercial_registry_photo' => ['required', 'url', 'max:255'],
                    'type' => ['required', 'in:stores,centers'],
                    'image' => ['required', 'url'],
                    'seller_id' => ['nullable']
                ];
                return $rules;
            }
            case 'PUT':
            case 'PATCH': {
                return [
                   'reason'=>'nullable',
                   'deactivation_reason'=>'nullable',
                ];
            }
            default:break;
        }
    }
}
