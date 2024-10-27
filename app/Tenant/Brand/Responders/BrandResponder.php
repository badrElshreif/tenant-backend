<?php

namespace App\Tenant\Brand\Responders;

use App\Infrastructure\Responders\Responder;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use App\Tenant\Brand\Domain\Resources\BrandLiteResource;
use App\Infrastructure\Traits\RESTApi;
use App\Infrastructure\Traits\ApiPaginator;
use App\Tenant\Category\Domain\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;

class BrandResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {

//            return $this->sendJson($this->response->getData(), $this->response->getStatus());
//
//        if ($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
//            return $this->response->getData();

    }
}
