<?php

namespace App\Tenant\Order\Actions\Cart;
use App\Tenant\Order\Domain\Requests\CartFormRequest;
use App\Tenant\Order\Domain\Services\Cart\ListUserCartService;
use App\Tenant\Order\Responders\CartResponder;

class ListUserCartAction
{
    public function __construct(CartResponder $responder, ListUserCartService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(CartFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request)
        )->respond();
    }
}
