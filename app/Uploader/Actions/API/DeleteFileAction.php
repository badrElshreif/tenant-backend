<?php

namespace App\Uploader\Actions\API;
use App\Uploader\Domain\Requests\DeleteFileFormRequest;
use App\Uploader\Domain\Services\API\DeleteFileService;
use App\Uploader\Responders\API\DeleteFileResponder;

class DeleteFileAction 
{
    public function __construct(DeleteFileResponder $responder, DeleteFileService $services) 
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(DeleteFileFormRequest $request) 
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}