<?php

namespace App\Tenant\Admin\Responders;

use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Infrastructure\Traits\ApiPaginator;
use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Traits\RESTApi;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\Admin\Domain\Resources\RoleResource;
use App\Tenant\Admin\Domain\Resources\RoleLiteResource;


class RoleResponder extends Responder implements ResponderInterface
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if(!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))

        	return $this->sendError($this->response->getData());

        if($this->response->getStatus() == Response::HTTP_CREATED)
            return $this->sendJson(
                new RoleResource($this->response->getData()),
                $this->response->getStatus()
            );

        if($this->response->getStatus() == Response::HTTP_OK)
            return $this->sendJson(
                RoleLiteResource::collection($this->response->getData()),
                $this->response->getStatus()
            );

        if($this->response->getStatus() == Response::HTTP_ACCEPTED)

            return $this->sendJson(
                $this->getPaginatedResponse(
                    $this->response->getData(),
                    RoleLiteResource::collection($this->response->getData())
                )
            );

        if($this->response->getStatus() == Response::HTTP_NO_CONTENT)

            return $this->sendJson($this->response->getData(), $this->response->getStatus());

    }
}
