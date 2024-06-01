<?php

namespace App\Tenant\Order\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use App\Tenant\Order\Domain\Models\PaymentMethod;
use Illuminate\Validation\Rule;
class ServiceOrderRequest extends CustomApiRequest
{
    public function rules()
    {
            switch ($this->method()) {
                case 'GET': {
                    return [
                        'per_page' => ['sometimes', 'nullable', 'numeric', 'gte:1'],
                        'type' => ['required', 'in:stores,centers']
                    ];
                }
                case 'DELETE': {
                    return [];
                }
                case 'POST': {
                    $rules = [
                        'user_address_id' => ['required', 'numeric', 'exists:user_addresses,id'],
                        'payment_method_id' => ['nullable', 'numeric', 'exists:payment_methods,id'],
                        //'store_id' => ['required', 'numeric', 'exists:stores,id'],
                        'service_wanted_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                        'service_id' => ['required', 'numeric', 'exists:products,id'],
                        'subcategory_id' => ['required', 'numeric', 'exists:categories,id'],
                        'problem_description' => ['required', 'string', 'min:1', 'max:1000'],
                        'notes' => ['nullable', 'string', 'max:1000'],
                        'promo_code'  => ['nullable', 'string', 'max:255', 'exists:promo_codes,code'],
                        'depositor_name' => ['nullable', 'string', 'min:1', 'max:255'],
                        'deposit_amount' => ['nullable', 'regex:/^\d*(\.\d{3})?$/'],
                        'deposit_receipt' => ['nullable', 'string', 'min:1', 'max:255'],
                        'bank_account_id' => ['nullable', 'numeric', 'exists:bank_accounts,id'],
                        'use_wallet' => ['required', 'boolean'],
                    ];
                    if(auth()->guard('admin')->check()){
                        $rules['user_id'] = ['required', 'numeric', 'exists:users,id'];
                    }
                    if($this->payment_method_id){
                        $payment_method = PaymentMethod::findOrFail($this->payment_method_id);
                        if($payment_method->type=='online'){
                            $rules['online_payment_method_id'] = ['required', 'numeric'];
                            $rules['payment_type'] = ['nullable', Rule::in(['web','mobile'])];
                        }
                    }
                    return $rules;
                }
                case 'PUT':
                case 'PATCH': {
                    $rules = [
                        'admin_notes'  => ['sometimes', 'nullable', 'string', 'min:1', 'max:1000'],
                        'user_address_id' => ['sometimes', 'numeric', 'exists:user_addresses,id'],
                        'store_id' => ['sometimes', 'numeric', 'exists:stores,id'],
                        'service_wanted_date' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                        'service_id' => ['sometimes', 'numeric', 'exists:products,id'],
                        'subcategory_id' => ['sometimes', 'numeric', 'exists:categories,id'],
                        'problem_description' => ['sometimes', 'string', 'min:1', 'max:1000'],
                        'payment_method_id' => ['sometimes', 'numeric', 'exists:payment_methods,id'],
                        'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
                        'promo_code'  => ['sometimes', 'nullable', 'string', 'max:255', 'exists:promo_codes,code'],
                        'depositor_name' => ['sometimes', 'string', 'min:1', 'max:255'],
                        'deposit_amount' => ['sometimes', 'regex:/^\d*(\.\d{3})?$/'],
                        'deposit_receipt' => ['sometimes', 'string', 'min:1', 'max:255'],
                        'bank_account_id' => ['sometimes', 'numeric', 'exists:bank_accounts,id'],
                        'user_id' => ['sometimes', 'numeric', 'exists:users,id'],
                        'use_wallet' => ['required', 'boolean'],
                    ];

                    return $rules;
                }
                default:break;

                }
    }
}
