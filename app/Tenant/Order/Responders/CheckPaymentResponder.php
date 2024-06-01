<?php

namespace App\Tenant\Order\Responders;

use App\Infrastructure\Responders\Responder;
use App\Tenant\Order\Domain\Resources\OnlinePaymentMethodResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Infrastructure\Helpers\Traits\RESTApi;
use App\Infrastructure\Helpers\Traits\ApiPaginator;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentResponder extends Responder
{
    use RESTApi, ApiPaginator;

    public function respond()
    {
        if (!in_array($this->response->getStatus(), array_values(config('statuses.SUCCESS'))))
            return $this->sendError($this->response->getData());

        if ($this->response->getStatus() == Response::HTTP_CREATED) {
            return $this->sendJson(
                OnlinePaymentMethodResource::collection($this->response->getData()),
                Response::HTTP_OK
            );
        }

        if ($this->response->getStatus() == Response::HTTP_OK)
            return $this->sendJson(
                OnlinePaymentMethodResource::collection($this->response->getData()),
                $this->response->getStatus()
            );

        if ($this->response->getStatus() == Response::HTTP_ACCEPTED)
            return redirect()->to($this->response->getData()['route']);

        if ($this->response->getStatus() == Response::HTTP_CONTINUE)
            return redirect()->to($this->response->getData()['route'])
                ->with(['error' => $this->response->getData()['error']])
                ;

        if ($this->response->getStatus() == Response::HTTP_NO_CONTENT)
            return $this->sendJson($this->response->getData(), $this->response->getStatus());

        if ($this->response->getStatus() == Response::HTTP_RESET_CONTENT)
            return $this->response->getData();

    }
}
