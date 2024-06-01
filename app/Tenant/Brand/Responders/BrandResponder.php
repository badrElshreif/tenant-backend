<?php

namespace App\Tenant\Brand\Responders;

use App\Infrastructure\Responders\Responder;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use App\Tenant\Brand\Domain\Resources\BrandLiteResource;
use App\Infrastructure\Helpers\Traits\RESTApi;
use App\Infrastructure\Helpers\Traits\ApiPaginator;
use Symfony\Component\HttpFoundation\Response;

class BrandResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if (!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))
            return $this->sendError($this->response->getData());

        if ($this->response->getStatus() == Response::HTTP_CREATED)
            return $this->sendJson(
                new BrandResource($this->response->getData()),
                Response::HTTP_OK
            );

        if ($this->response->getStatus() == Response::HTTP_OK)
            return $this->sendJson(
                BrandLiteResource::collection($this->response->getData()),
                $this->response->getStatus()
            );

        if ($this->response->getStatus() == Response::HTTP_ACCEPTED) {

            if (request()->is_paginated == 1) {
                return $this->getPaginatedResponse(
                    $this->response->getData(),
                    BrandLiteResource::collection($this->response->getData())
                );
            }
            return $this->sendJson(
                BrandLiteResource::collection($this->response->getData()),
                $this->response->getStatus()
            );
        }

        if ($this->response->getStatus() == Response::HTTP_NO_CONTENT)
            return $this->sendJson($this->response->getData(), $this->response->getStatus());

        if ($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
            return $this->response->getData();

    }
}
