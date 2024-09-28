<?php

namespace App\Tenant\AppContent\Responders\API;

use App\Infrastructure\Responders\Responder;
use App\Tenant\AppContent\Domain\Resources\FaqResource;
use App\Infrastructure\Helpers\Traits\RESTApi;
use Symfony\Component\HttpFoundation\Response;

class UpdateFaqResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError($this->response->getData());
        return $this->sendJson(
        	new FaqResource($this->response->getData()),
        	$this->response->getStatus()
        );

    }
}
