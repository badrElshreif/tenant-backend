<?php

namespace App\Tenant\Admin\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;

class LogoutAdminResponder extends Responder implements ResponderInterface
{
    public function respond()
    {
        return response()->json($this->response->getData(), $this->response->getStatus());
    }
}
