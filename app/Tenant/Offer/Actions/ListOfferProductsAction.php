<?php

namespace App\Tenant\Offer\Actions;
use App\Tenant\Offer\Domain\Requests\OfferRequest;
use App\Tenant\Offer\Domain\Services\ListOfferProductsService;
use App\Product\Responders\ProductResponder;

class ListOfferProductsAction
{
    public function __construct(ProductResponder $responder, ListOfferProductsService $service)
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
