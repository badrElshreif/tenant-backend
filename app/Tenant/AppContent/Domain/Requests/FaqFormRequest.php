<?php

namespace App\Tenant\AppContent\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class FaqFormRequest extends CustomApiRequest
{
    public function rules()
    {

        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                return [
                    'en.question' => [
                        'required',
                        'max:255',
                         Rule::unique('faq_translations', 'question')
                             ->where(function ($query) {
                                 $query->where('locale', 'en');
                             })
                    ],
                    'ar.question' => [
                        'required',
                        'max:255',
                         Rule::unique('faq_translations', 'question')
                             ->where(function ($query) {
                                 $query->where('locale', 'ar');
                             })
                    ],
                    'en.answer' => [
                        'required',
                        'max:1000',
                         // Rule::unique('faq_translations', 'answer')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'en');
                         //     })
                    ],
                    'ar.answer' => [
                        'required',
                        'max:1000',
                         // Rule::unique('faq_translations', 'answer')
                         //     ->where(function ($query) {
                         //         $query->where('locale', 'ar');
                         //     })
                    ],
                    'is_active' => ['sometimes', 'boolean']

                ];
            }
            case 'PUT':
            case 'PATCH': {
                $id = $this->id;
                return [
                    'en.question' => [
                        'required',
                        'max:255',
                         Rule::unique('faq_translations', 'question')
                             ->where(function ($query) use($id){
                                 $query->where('locale', 'en')->where('faq_id','!=',$id);
                             })
                    ],
                    'ar.question' => [
                        'required',
                        'max:255',
                         Rule::unique('faq_translations', 'question')
                             ->where(function ($query) use($id) {
                                 $query->where('locale', 'ar')->where('faq_id','!=',$id);
                             })
                    ],
                    'en.answer' => [
                        'required',
                        'max:255',
                         Rule::unique('faq_translations', 'answer')
                             ->where(function ($query) use($id){
                                 $query->where('locale', 'en')->where('faq_id','!=',$id);
                             })
                    ],
                    'ar.answer' => [
                        'required',
                        'max:255',
                         Rule::unique('faq_translations', 'answer')
                             ->where(function ($query) use($id) {
                                 $query->where('locale', 'ar')->where('faq_id','!=',$id);
                             })
                    ],
                   'is_active' => ['sometimes', 'boolean']

                ];
            }
            default:break;
        }
    }

}
