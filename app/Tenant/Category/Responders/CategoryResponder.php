<?php

namespace App\Tenant\Category\Responders;

use App\Infrastructure\Responders\Responder;
use App\Tenant\Category\Domain\Resources\CategoryResource;
use App\Tenant\Category\Domain\Resources\CategoryLiteResource;
use App\Infrastructure\Traits\RESTApi;
use App\Infrastructure\Traits\ApiPaginator;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Enums\ResponseType;

class CategoryResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        //return $this->getResponseData();
//        return $this->sendJson($this->response->getData(), Response::HTTP_OK);
    }

}
