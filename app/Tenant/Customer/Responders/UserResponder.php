<?php

namespace App\User\Responders;

use App\Infrastructure\Responders\Responder;
use App\User\Domain\Resources\UserResource;
use App\User\Domain\Resources\UserLiteResource;
use App\Infrastructure\Helpers\Traits\RESTApi;
use App\Infrastructure\Helpers\Traits\ApiPaginator;
use Symfony\Component\HttpFoundation\Response;

class UserResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if($this->response->getStatus() == 425)
            return $this->sendJson([
                'error' => $this->response->getData(),
                'is_active' => false
            ], 
            $this->response->getStatus()
        );

        if(!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))
            return $this->sendError($this->response->getData(), $this->response->getStatus());

        if($this->response->getStatus() == Response::HTTP_CREATED)
            return $this->sendJson(
                new UserResource($this->response->getData()),
                Response::HTTP_OK
            );

        if($this->response->getStatus() == Response::HTTP_OK)
            return $this->sendJson(
                UserLiteResource::collection($this->response->getData()),
                $this->response->getStatus()
            );
        
        if($this->response->getStatus() == Response::HTTP_ACCEPTED)
            return $this->sendJson(
                $this->getPaginatedResponse(
                    $this->response->getData(),
                    UserLiteResource::collection($this->response->getData())
                ), Response::HTTP_OK
            );

        if($this->response->getStatus() == Response::HTTP_NO_CONTENT)
            return $this->sendJson($this->response->getData(), $this->response->getStatus());

        if($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
            return $this->response->getData();

    }
}