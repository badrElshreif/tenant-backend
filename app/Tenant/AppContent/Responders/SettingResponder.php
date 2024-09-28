<?php

namespace App\Tenant\AppContent\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Traits\ApiPaginator;
use App\Tenant\AppContent\Domain\Resources\SettingResource;
use App\Infrastructure\Traits\RESTApi;
use Symfony\Component\HttpFoundation\Response;

class SettingResponder extends Responder
{
    use ApiPaginator, RESTApi;

    public function respond()
    {
        if (!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))
            return $this->sendError($this->response->getData());

        if ($this->response->getStatus() == Response::HTTP_CREATED)
            return $this->sendJson(
                new SettingResource($this->response->getData()),
                Response::HTTP_OK
            );

        if ($this->response->getStatus() == Response::HTTP_OK)
            return $this->sendJson(
                SettingResource::collection($this->response->getData()),
                $this->response->getStatus()
            );

        if ($this->response->getStatus() == Response::HTTP_ACCEPTED)
            return $this->sendJson(
                SettingResource::collection($this->response->getData())
                , Response::HTTP_OK
            );

        if ($this->response->getStatus() == Response::HTTP_NO_CONTENT)
            return $this->sendJson($this->response->getData(), $this->response->getStatus());


        if ($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
            return $this->sendJson($this->response->getData(), Response::HTTP_OK);
    }
}
