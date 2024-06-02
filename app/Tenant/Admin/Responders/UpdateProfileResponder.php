<?php

namespace App\Tenant\Admin\Responders;

use App\Infrastructure\Responders\Responder;
use App\Tenant\Admin\Domain\Resources\AdminResource;
use App\Infrastructure\Traits\RESTApi;

class UpdateProfileResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError($this->response->getData());
        $admin = $this->response->getData();
        return $this->sendJson(new AdminResource($admin), $this->response->getStatus());

    }
}
