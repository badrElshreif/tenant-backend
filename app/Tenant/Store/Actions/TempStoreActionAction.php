<?php

namespace App\Tenant\Store\Actions;
use App\Tenant\Store\Domain\Requests\TemStoreActionRequest;
use App\Tenant\Store\Domain\Services\TempStoreActionService;
use App\Tenant\Store\Responders\StoreResponder;

class TempStoreActionAction
{
    public function __construct(StoreResponder $responder, TempStoreActionService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(TemStoreActionRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
