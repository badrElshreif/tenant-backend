<?php

namespace App\Uploader\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class UploadFileFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            //'file'   => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'file'   => ['required', 'mimes:jpeg,png,jpg,gif,txt,pdf,mpeg,ogg,mp4,webm,3gp,mov,flv,mkv,wmv'],
            'path'   => ['required'],
            'width'   => ['nullable', 'numeric', 'gt:50', 'lte:20000'],
            'height'   => ['nullable', 'numeric', 'gt:50', 'lte:20000']
        ];
    }
}
