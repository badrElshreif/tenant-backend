<?php

namespace App\Uploader\Actions\API;
use App\Uploader\Domain\Requests\UploadFileFormRequest;
use App\Uploader\Domain\Services\API\UploadFileService;
use App\Uploader\Responders\API\UploadFileResponder;

class UploadFileAction 
{
    public function __construct(UploadFileResponder $responder, UploadFileService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(UploadFileFormRequest $request) 
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
