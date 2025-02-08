<?php

namespace App\User\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Helpers\Traits\RESTApi;

class LoginUserResponder extends Responder implements ResponderInterface
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200 && $this->response->getStatus() != 425
        	return $this->sendError(
        		$this->response->getData(),
        		$this->response->getStatus()
        	);

        if($this->response->getStatus() == 425)
            return $this->sendMessage([
                $this->response->getData(),
                'is_active': false
            ],
            $this->response->getStatus()
        );
        return $this->sendJson(
        	$this->response->getData(),
        	$this->response->getStatus()
        );
    }
}
