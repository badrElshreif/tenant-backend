<?php

namespace App\Infrastructure\Responders;

use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Traits\RESTApi;

class GenericListResponder extends Responder implements ResponderInterface
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() == 204)

            return $this->sendJson($this->response->getData(), 200);

        return $this->sendJson(
        	GenericNameResource::collection($this->response->getData()),
        	$this->response->getStatus()
        );
    }
}
