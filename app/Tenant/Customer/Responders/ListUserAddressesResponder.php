<?php

namespace App\User\Responders;

use App\Infrastructure\Responders\Responder;
use App\Infrastructure\Helpers\Traits\RESTApi;
use App\User\Domain\Resources\UserAddressResource;
use App\User\Domain\Resources\UserAddressAdminResource;

class ListUserAddressesResponder extends Responder
{
    use RESTApi;

    public function respond()
    {
        if($this->response->getStatus() != 200)
        	return $this->sendError($this->response->getData());

        // if(auth()->guard('admin')->check()){
        // 	return $this->sendJson(
	       //      UserAddressAdminResource::collection($this->response->getData())
	       //  );
        // }

        return $this->sendJson(
        	UserAddressResource::collection($this->response->getData())
        );
    }
}