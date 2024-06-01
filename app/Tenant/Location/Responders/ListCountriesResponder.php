<?php

namespace App\Tenant\Location\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Helpers\Traits\RESTApi;
use App\Infrastructure\Helpers\Traits\ApiPaginator;
use App\Tenant\Location\Domain\Resources\CountryLiteResource;
use App\Tenant\Location\Domain\Resources\CountryResource;

class ListCountriesResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if (!auth()->guard('admin')->check() && $this->response->getStatus() == 200)
            return $this->sendJson(
                CountryLiteResource::collection($this->response->getData())
            );
        if (auth()->guard('admin')->check() && $this->response->getStatus() == 200)
            return $this->sendJson(
                CountryLiteResource::collection($this->response->getData())
            );
        return $this->sendJson(
            $this->getPaginatedResponse(
                $this->response->getData(),
                CountryResource::collection($this->response->getData())
            )
        );
    }
}
