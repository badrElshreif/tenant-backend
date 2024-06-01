<?php

namespace App\Tenant\Store\Actions;
use App\Tenant\Store\Domain\Requests\StoreFormRequest;
use App\Tenant\Store\Domain\Services\ListStoresService;
use App\Tenant\Store\Responders\StoreResponder;

class ListStoresAction
{
    public function __construct(StoreResponder $responder, ListStoresService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(StoreFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
