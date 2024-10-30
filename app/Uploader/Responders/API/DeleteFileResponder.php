<?php

namespace App\Uploader\Responders\API;

use App\Infrastructure\Responders\Responder;
use App\Admin\Domain\Resources\AdminResource;
use App\Infrastructure\Helpers\Traits\RESTApi;

class DeleteFileResponder extends Responder
{
    use RESTApi;
    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError($this->response->getData(), $this->response->getStatus());
        return $this->sendJson($this->response->getData());
    }
}