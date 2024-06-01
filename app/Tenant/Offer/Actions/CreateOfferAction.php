<?php

namespace App\Tenant\Offer\Actions;
use App\Tenant\Offer\Domain\Requests\OfferRequest;
use App\Tenant\Offer\Domain\Services\CreateOfferService;
use App\Tenant\Offer\Responders\OfferResponder;

class CreateOfferAction
{
    public function __construct(OfferResponder $responder, CreateOfferService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(OfferRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
