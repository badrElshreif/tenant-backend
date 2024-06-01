<?php

namespace App\Tenant\Store\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class UpdateBankDataRequest extends CustomApiRequest
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
                    'bank_type' => ['required', 'in:local,global'],
                    'bank_user_name' => ['required', 'string','max:255'],
                    'bank_name' => ['required', 'string','max:255'],
                    'bank_account_no' => ['required_if:bank_type,local', 'string','max:255'],
                    'iban_no' => ['required_if:bank_type,local',Rule::unique('stores','iban_no')->ignore(auth()->user()->store_id), 'string', 'max:255'],
                    'swift_code' => ['required_if:bank_type,global', 'string', 'max:255'],
                    'bank_country_id' => ['required_if:bank_type,global', 'exists:countries,id'],
                ];
                return $rules;
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'bank_type' => ['required', 'in:local,global'],
                    'bank_user_name' => ['required', 'string','max:255'],
                    'bank_name' => ['required', 'string','max:255'],
                    'bank_account_no' => ['required_if:bank_type,local', 'string','max:255'],
                    'iban_no' => ['required_if:bank_type,local',Rule::unique('stores','iban_no')->ignore(auth('store')->user()->store->id), 'string', 'max:255'],
                    'swift_code' => ['required_if:bank_type,global',Rule::unique('stores','swift_code')->ignore(auth('store')->user()->store->id), 'string', 'max:255'],
                    //'swift_code' => ['required_if:bank_type,global', 'string', 'max:255'],
                    'bank_user_phone' => ['required_if:bank_type,global', 'string', 'max:255'],
                    'bank_address' => ['required_if:bank_type,global', 'string', 'max:255'],
                    'bank_country_id' => ['required_if:bank_type,global', 'exists:countries,id'],
                ];
            }
            default:break;
        }
    }
}
