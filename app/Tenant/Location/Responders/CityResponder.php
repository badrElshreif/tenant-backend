<?php

namespace App\Tenant\Location\Responders;

use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Infrastructure\Helpers\Traits\ApiPaginator;
use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Helpers\Traits\RESTApi;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\Location\Domain\Resources\CityResource;
use App\Tenant\Location\Domain\Resources\CityLiteResource;


class CityResponder extends Responder implements ResponderInterface
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if(!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))

        	return $this->sendError($this->response->getData());

        if($this->response->getStatus() == Response::HTTP_CREATED)
            return $this->sendJson(
                new CityResource($this->response->getData()),
                $this->response->getStatus()
            );

        if($this->response->getStatus() == Response::HTTP_OK)
            return $this->sendJson(
                GenericNameResource::collection($this->response->getData()),
                $this->response->getStatus()
            );

        if($this->response->getStatus() == Response::HTTP_ACCEPTED){
            if (request()->is_paginated == 1) {
                return $this->getPaginatedResponse(
                    $this->response->getData(),
                    CityLiteResource::collection($this->response->getData())
                );
            }
            return $this->sendJson(
                CityLiteResource::collection($this->response->getData())
            );
        }


        if($this->response->getStatus() == Response::HTTP_NO_CONTENT)

            return $this->sendJson($this->response->getData(), $this->response->getStatus());

    }
}
