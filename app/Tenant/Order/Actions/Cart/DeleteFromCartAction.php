<?php

namespace App\Tenant\Order\Actions\Cart;
use App\Tenant\Order\Domain\Services\Cart\DeleteFromCartService;
use App\Tenant\Order\Responders\CartResponder;
use App\Tenant\Order\Domain\Requests\CartFormRequest;
class DeleteFromCartAction
{
    public function __construct(CartResponder $responder, DeleteFromCartService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(CartFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
