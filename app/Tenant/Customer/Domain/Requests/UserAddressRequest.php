<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class UserAddressRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                    ];
                }
                case 'DELETE': {
                    return [];
                }
                case 'POST': {
                    return [
                        'title' => ['sometimes', 'min:3', 'max:250', 'string',
                        Rule::unique('user_addresses', 'title')
                            ->where(function ($query) {
                                $query->where('user_id',auth()->id());
                            })
                        ],
                        'phone' => ['sometimes', 'digits_between:7,17'],
                        'address'=>['required', 'string'],
                        'is_primary'=>['sometimes', 'boolean'],
                        'country_id' => ['required', 'numeric'],
                        'state_id' => ['required', 'numeric'],
                        'city_id' => ['required', 'numeric'],
                        'latitude' => ['required'],
                        'longitude' => ['required']
                    ];
                }
                case 'PUT':
                case 'PATCH': {
                    return [
                        'title' => ['sometimes', 'min:3', 'max:250', 'string',
                        Rule::unique('user_addresses', 'title')
                            ->where(function ($query) {
                                $query->where('user_id',auth()->id())->where('id','!=',$this->id);
                            })
                        ],
                        'phone' => ['sometimes', 'digits_between:7,17'],
                        'address'=>['sometimes', 'string'],
                        'is_primary'=>['sometimes', 'boolean'],
                        'country_id' => ['sometimes', 'numeric'],
                        'state_id' => ['sometimes', 'numeric'],
                        'city_id' => ['sometimes', 'numeric'],
                        'latitude' => ['nullable'],
                        'longitude' => ['nullable']
                    ];
                }
                default:break;
    
                }
    }
}
