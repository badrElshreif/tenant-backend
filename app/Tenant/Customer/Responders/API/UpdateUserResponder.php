<?php

namespace App\User\Responders\API;

use App\Infrastructure\Responders\Responder;
use App\User\Domain\Resources\UserResource;
use App\Infrastructure\Helpers\Traits\RESTApi;

class UpdateUserResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError($this->response->getData());
        $user = $this->response->getData();
        return $this->sendJson(new UserResource($user), $this->response->getStatus());

    }
}