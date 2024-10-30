<?php

namespace App\Uploader\Domain\Requests;

use App\Infrastructure\Http\Requests\API\CustomApiRequest;

class UploadMultipleFilesFormRequest extends CustomApiRequest
{
    public function rules()
    {
        return [
            'files'   => ['required', 'array'],
            'path'   => ['required', 'string'],
            //'files.*'   => ['required','mimes:jpeg,png,jpg,gif,svg,csv,txt,xlx,xls,pdf,mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts'],
            'files.*'   => ['required', 'mimes:jpeg,png,jpg,gif,txt,pdf,mpeg,ogg,mp4,webm,3gp,mov,flv,mkv,wmv'],
            'width'   => ['nullable', 'numeric', 'gt:50', 'lte:20000'],
            'height'   => ['nullable', 'numeric', 'gt:50', 'lte:20000']
        ];
    }
}
