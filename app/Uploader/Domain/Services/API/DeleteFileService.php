<?php

namespace App\Uploader\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\FileUploadPath;
use App\Infrastructure\Helpers\Traits\UploaderHelper;

class DeleteFileService extends Service
{
    use UploaderHelper;
    public function handle($data = []) 
    {
        $file = $this->deleteFile($data['file'], $data['path']);
        return new GenericPayload(['message' => __('success.deletedSuccessfuly')]);
    }
}