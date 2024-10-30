<?php

namespace App\Uploader\Actions;
use App\Uploader\Domain\Requests\DeleteFileFormRequest;
use App\Uploader\Domain\Services\DeleteAttachmentService;
use App\Uploader\Responders\API\DeleteFileResponder;

class DeleteAttachmentAction 
{
    public function __construct(DeleteFileResponder $responder, DeleteAttachmentService $service) 
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id) 
    {
        return $this->responder->withResponse(
            $this->service->handle(['attachment_id' => $id])
        )->respond();
    }
}