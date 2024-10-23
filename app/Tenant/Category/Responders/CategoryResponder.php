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
        $type = $this->response->getType();
        if ($type == 'collection_with_pagination') {
            return $this->sendJson($this->getPaginatedResponse(
                $this->response->getData(),
                CategoryResource::collection($this->response->getData())
            ));
        } else if ($type == 'collection_list') {
            return $this->sendJson(
                CategoryLiteResource::collection($this->response->getData()),
                $this->response->getStatus()
            );
        } else if ($type == 'resource') {
            return $this->sendJson(
                new CategoryResource($this->response->getData()),
                $this->response->getStatus()
            );
        } else if ($type == 'error') {
            return $this->sendError($this->response->getData(), $this->response->getStatus());
        }

        return $this->sendJson($this->response->getData(), Response::HTTP_OK);
    }
}
