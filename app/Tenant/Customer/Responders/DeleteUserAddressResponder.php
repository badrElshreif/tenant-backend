<?php

namespace App\User\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Helpers\Traits\RESTApi;

class DeleteUserAddressResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError($this->response->getData());
        return $this->sendJson($this->response->getData());
    }
}