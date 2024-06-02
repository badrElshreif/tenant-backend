<?php

namespace App\Tenant\Order\Responders;

use App\Infrastructure\Responders\Responder;
use App\Tenant\Order\Domain\Resources\PaymentLiteResource;
use App\Tenant\Order\Domain\Resources\PaymentResource;
use App\Infrastructure\Traits\RESTApi;
use App\Infrastructure\Traits\ApiPaginator;
use Symfony\Component\HttpFoundation\Response;

class PaymentResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if(!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))
            return $this->sendError($this->response->getData());

        if($this->response->getStatus() == Response::HTTP_CREATED){
            return $this->sendJson(
                new PaymentResource($this->response->getData()),
                Response::HTTP_OK
            );
        }

        if($this->response->getStatus() == Response::HTTP_OK)
            return $this->sendJson(
                PaymentResource::collection($this->response->getData()),
                $this->response->getStatus()
            );

        if($this->response->getStatus() == Response::HTTP_ACCEPTED)
            return $this->sendJson(
                $this->getPaginatedResponse(
                    $this->response->getData(),
                    PaymentLiteResource::collection($this->response->getData())
                ), Response::HTTP_OK
            );

        if($this->response->getStatus() == Response::HTTP_NO_CONTENT)
            return $this->sendJson($this->response->getData(), $this->response->getStatus());

        if($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
            return $this->response->getData();

    }
}
