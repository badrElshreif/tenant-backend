<?php

namespace App\Uploader\Actions\API;
use App\Uploader\Domain\Requests\UploadMultipleFilesFormRequest;
use App\Uploader\Domain\Services\API\UploadMultipleFilesService;
use App\Uploader\Responders\API\UploadMultipleFilesResponder;

class UploadMultipleFilesAction 
{
    public function __construct(UploadMultipleFilesResponder $responder, UploadMultipleFilesService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(UploadMultipleFilesFormRequest $request) 
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}