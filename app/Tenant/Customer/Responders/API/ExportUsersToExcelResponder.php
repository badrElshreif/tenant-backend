<?php

namespace App\User\Responders\API;

use App\Infrastructure\Responders\Responder;

use App\Infrastructure\Helpers\Traits\RESTApi;
class ExportUsersToExcelResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        return $this->response->getData();
    }
}