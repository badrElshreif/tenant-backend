<?php

namespace App\Tenant\Location\Responders;

use App\Infrastructure\Traits\ApiPaginator;
use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Responders\ResponderInterface;
use App\Infrastructure\Traits\RESTApi;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\Location\Domain\Resources\CountryResource;
use App\Tenant\Location\Domain\Resources\CountryLiteResource;


class CountryResponder extends Responder implements ResponderInterface
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if (!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))
            return $this->sendError($this->response->getData());


        if ($this->response->getStatus() == Response::HTTP_CREATED)
            return $this->sendJson(
                new CountryResource($this->response->getData()),
                $this->response->getStatus()
            );


        if ($this->response->getStatus() == Response::HTTP_ACCEPTED) {
            if (request()->is_paginated == 1) {
                return $this->sendJson($this->getPaginatedResponse(
                    $this->response->getData(),
                    CountryResource::collection($this->response->getData())
                ));
            }
            return $this->sendJson(
                CountryLiteResource::collection($this->response->getData())->resource
            );
        }

        if ($this->response->getStatus() == Response::HTTP_NO_CONTENT)
            return $this->sendJson($this->response->getData(), $this->response->getStatus());

    }
}
