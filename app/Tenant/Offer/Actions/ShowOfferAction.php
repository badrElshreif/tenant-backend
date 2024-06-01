<?php

namespace App\Tenant\Offer\Actions;
use App\Tenant\Offer\Domain\Services\ShowOfferService;
use App\Tenant\Offer\Responders\OfferResponder;

class ShowOfferAction
{
    public function __construct(OfferResponder $responder, ShowOfferService $service)
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
