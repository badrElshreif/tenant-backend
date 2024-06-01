<?php

namespace App\Tenant\Order\Actions\Cart;
use App\Tenant\Order\Domain\Requests\AddToUserCartFormRequest;
use App\Tenant\Order\Domain\Services\Cart\AddToUserCartService;
use App\Tenant\Order\Responders\CartResponder;

class AddToUserCartAction
{
    public function __construct(CartResponder $responder, AddToUserCartService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(AddToUserCartFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
