<?php

namespace App\User\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Helpers\Traits\RESTApi;

class ChangePasswordResponder extends Responder implements ResponderInterface
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError(
        		$this->response->getData(),
        		$this->response->getStatus()
        	);
        return $this->sendJson(
        	$this->response->getData(), 
        	$this->response->getStatus()
        );
    }
}
