<?php

namespace App\Tenant\Offer\Actions;
use App\Tenant\Offer\Domain\Requests\OfferRequest;
use App\Tenant\Offer\Domain\Services\ListOffersService;
use App\Tenant\Offer\Responders\OfferResponder;

class ListOffersAction
{
    public function __construct(OfferResponder $responder, ListOffersService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(OfferRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request)
        )->respond();
    }
}
