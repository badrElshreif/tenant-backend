<?php

namespace App\Tenant\AppContent\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class SettingRequest extends CustomApiRequest
{
    public function rules()
    {

        switch ($this->method()) {
            case 'GET':  {
                return [
                    'target' => [
                        'nullable', 'in:superAdmin,stores,centers',
                        Rule::requiredIf(function () use ($category){
                            return !auth()->check();
                        }),
                    ],
                ];
            }
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                return [
                    'settings' => ['required', 'array'],
                    'settings.*.en.name' => [
                        'nullable',
                        'max:255',
                         // Rule::unique('setting_translations', 'name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'en');
                         //     })
                    ],
                    'settings.*.ar.name' => [
                        'nullable',
                        'max:255',
                         // Rule::unique('setting_translations', 'name')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'ar');
                         //     })
                    ],
                    'settings.*.key' => ['required', 'string', 'min:1', 'max:255'],
                    'settings.*.body' => ['required', 'max:1000'],
                    'settings.*.property_type_id' => ['nullable', 'numeric', 'exists:property_types,id'],
                    'settings.*.is_active' => ['nullable', 'boolean']
                ];
            }
            case 'PUT':
            case 'PATCH': {
                $id = $this->id;
                return [
                    'en.name' => [
                        'nullable',
                        'max:255',
                         Rule::unique('setting_translations', 'name')
                             ->where(function ($query) use($id){
                                 $query->where('locale', 'en')->where('setting_id','!=',$id);
                             })
                    ],
                    'ar.name' => [
                        'nullable',
                        'max:255',
                         Rule::unique('setting_translations', 'name')
                             ->where(function ($query) use($id) {
                                 $query->where('locale', 'ar')->where('setting_id','!=',$id);
                             })
                    ],
                   'is_active' => ['nullable', 'boolean']

                ];
            }
            default:break;
        }
    }

}
