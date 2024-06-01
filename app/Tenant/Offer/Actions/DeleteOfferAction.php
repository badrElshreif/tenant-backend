<?php

namespace App\Tenant\Offer\Actions;
use App\Tenant\Offer\Domain\Services\DeleteOfferService;
use App\Tenant\Offer\Responders\OfferResponder;

class DeleteOfferAction
{
    public function __construct(OfferResponder $responder, DeleteOfferService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["offer_id" => $id])
        )->respond();
    }
}
