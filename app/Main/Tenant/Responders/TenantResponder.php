<?php

namespace App\Main\Tenant\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Traits\RESTApi;
use App\Infrastructure\Traits\ApiPaginator;
use Symfony\Component\HttpFoundation\Response;

class TenantResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        return $this->response->getData();
    }
}
