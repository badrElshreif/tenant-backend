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

            if (request()->is_paginated == 1) {

                return $this->sendJson($this->getPaginatedResponse(
                    $this->response->getData(),
                    BrandLiteResource::collection($this->response->getData())
                ));
            }else{
                return $this->sendJson($this->response->getData(), 200);
            }
            return $this->sendJson(
                BrandLiteResource::collection($this->response->getData()),
                $this->response->getStatus()
            );

//            return $this->sendJson($this->response->getData(), $this->response->getStatus());
//
//        if ($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
//            return $this->response->getData();

    }
}
