<?php

namespace App\User\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class UserAddressFormRequest extends CustomApiRequest
{
    // public function rules()
    // {
    //     $id = auth()->id();
    //     return [
    //         'title' => ['sometimes', 'min:3', 'max:250', 'string',
    //             Rule::unique('user_addresses', 'title')
    //                 ->where(function ($query) {
    //                     $query->where('user_id',auth()->id());
    //                 })
    //         ],
    //         'phone' => ['sometimes', 'digits_between:7,17'],
    //         'address'=>['required', 'string'],
    //         'is_primary'=>['sometimes', 'boolean'],
    //         'country_id' => ['required', 'numeric'],
    //         'state_id' => ['required', 'numeric'],
    //         'city_id' => ['required', 'numeric'],

    //     ];
    // }

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
                        'title' => ['required', 'min:3', 'max:250', 'string',
                        Rule::unique('user_addresses', 'title')
                            ->where(function ($query) {
                                $query->where('user_id',auth()->id());
                            })
                        ],
                        'phone' => ['nullable', 'digits_between:7,17'],
                        'address'=>['nullable', 'string'],
                        'is_primary'=>['sometimes', 'boolean'],
                        'country_id' => ['nullable', 'numeric', 'exists:countries,id'],
                        'state_id' => ['nullable', 'numeric', 'exists:states,id'],
                        'city_id' => ['nullable', 'numeric', 'exists:cities,id'],
                        'latitude' => ['required', 'string', 'min:1', 'max:255'],
                        'longitude' => ['required', 'string', 'min:1', 'max:255'],
                        'nearest_landmarks' => ['nullable', 'string', 'min:1', 'max:255']
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
                        'phone' => ['sometimes','nullable', 'digits_between:7,17'],
                        'address'=>['sometimes','nullable', 'string'],
                        'is_primary'=>['sometimes', 'boolean'],
                        'country_id' => ['sometimes','nullable', 'numeric', 'exists:countries,id'],
                        'state_id' => ['sometimes','nullable', 'numeric', 'exists:states,id'],
                        'city_id' => ['sometimes','nullable', 'numeric', 'exists:cities,id'],
                        'latitude' => ['nullable', 'string', 'min:1', 'max:255'],
                        'longitude' => ['nullable', 'string', 'min:1', 'max:255'],
                        'nearest_landmarks' => ['nullable', 'string', 'min:1', 'max:255']

                    ];
                }
                default:break;

                }
    }

    public function attributes()
    {
        return [
            'title' => __('general.addresses.title'),
        ];
    }
}
