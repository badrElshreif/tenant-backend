<?php

namespace App\Tenant\Category\Responders;

use App\Infrastructure\Responders\Responder;
use App\Tenant\Category\Domain\Resources\CategoryResource;
use App\Tenant\Category\Domain\Resources\CategoryLiteResource;
use App\Infrastructure\Traits\RESTApi;
use App\Infrastructure\Traits\ApiPaginator;
use Symfony\Component\HttpFoundation\Response;

class CategoryResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if (!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))
            return $this->sendError($this->response->getData());

        if ($this->response->getStatus() == Response::HTTP_CREATED)
            return $this->sendJson(
                new CategoryResource($this->response->getData()),
                Response::HTTP_OK
            );

        if ($this->response->getStatus() == Response::HTTP_OK) {

            if (request()->is_paginated == 1) {
                return $this->getPaginatedResponse(
                    $this->response->getData(),
                    CategoryLiteResource::collection($this->response->getData())
                );
            } else if (request()->main_category == 1) {
                return $this->sendJson(
                    CategoryLiteResource::collection($this->response->getData()),
                    $this->response->getStatus()
                );
            }

            return $this->sendJson(
                new CategoryResource($this->response->getData()),
                $this->response->getStatus()
            );
//            return $this->sendJson(
//                CategoryLiteResource::collection($this->response->getData()),
//                $this->response->getStatus()
//            );
        }

        if ($this->response->getStatus() == Response::HTTP_ACCEPTED) {
            if (request()->is_paginated == 1) {
                return $this->sendJson($this->getPaginatedResponse(
                    $this->response->getData(),
                    CategoryResource::collection($this->response->getData())
                ));
            }
            return $this->sendJson(
                CategoryResource::collection($this->response->getData())->resource
            );
        }


//        if($this->response->getStatus() == Response::HTTP_ACCEPTED)
//            return $this->sendJson(
//                $this->getPaginatedResponse(
//                    $this->response->getData(),
//                    CategoryLiteResource::collection($this->response->getData())
//                ), Response::HTTP_OK
//            );

        if ($this->response->getStatus() == Response::HTTP_NO_CONTENT)
            return $this->sendJson($this->response->getData(), Response::HTTP_OK);

        if ($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
            return $this->response->getData();

    }
}
