<?php

namespace App\Tenant\Admin\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Helpers\Traits\RESTApi;

class ChangePasswordResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200)
            return $this->sendError($this->response->getData());
        return $this->sendJson($this->response->getData());
    }
}
