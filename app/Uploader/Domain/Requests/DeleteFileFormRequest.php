<?php

namespace App\Uploader\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class DeleteFileFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'file'   => ['required', 'string'],
            'path'   => ['required']
        ];
    }
}
