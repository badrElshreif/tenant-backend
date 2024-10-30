<?php

namespace App\Uploader\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Utility\FileUploadPath;
use App\Infrastructure\Helpers\Traits\UploaderHelper;

class UploadMultipleFilesService extends Service
{
    use UploaderHelper;
    public function handle($data = []) 
    {
        $files = [];
        $width = isset($data['width']) ? $data['width'] : null;
        $height = isset($data['height']) ? $data['height'] : null;
        foreach ($data['files'] as $file) {
        	$uploaded_file = $this->uploadImage($file, $data['path'], $width, $height);
        	array_push($files, $uploaded_file);
        }
        
        return new GenericPayload($files);
    }
}