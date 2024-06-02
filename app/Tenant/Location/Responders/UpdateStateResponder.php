<?php

namespace App\Tenant\Location\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Traits\RESTApi;
use App\Tenant\Location\Domain\Resources\StateResource;

class UpdateStateResponder extends Responder
{
    use RESTApi;
    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError($this->response->getData());
        return $this->sendJson(
        	new StateResource($this->response->getData()),
        	$this->response->getStatus()
        );
    }
}
