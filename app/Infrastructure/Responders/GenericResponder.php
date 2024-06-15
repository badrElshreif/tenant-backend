<?php

namespace App\Infrastructure\Responders;

use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Traits\RESTApi;

class GenericResponder extends Responder implements ResponderInterface
{
    use RESTApi;

    public function respond()
    {
        return $this->sendJson($this->response->getData(), $this->response->getSatusCode() ?? 200);
    }
}
