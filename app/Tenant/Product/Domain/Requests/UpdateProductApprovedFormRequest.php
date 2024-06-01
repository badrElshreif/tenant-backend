<?php

namespace App\Tenant\Product\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;
use Illuminate\Validation\Rule;

class UpdateProductApprovedFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'deactivation_start_date' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'deactivation_end_date' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:deactivation_start_date']
        ];
    }
}
