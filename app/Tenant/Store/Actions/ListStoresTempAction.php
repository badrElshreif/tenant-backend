<?php

namespace App\Tenant\Store\Actions;
use App\Tenant\Store\Domain\Requests\StoreFormRequest;
use App\Tenant\Store\Domain\Services\ListStoresService;
use App\Tenant\Store\Domain\Services\ListStoresTempService;
use App\Tenant\Store\Responders\StoreResponder;
use App\Tenant\Store\Responders\StoreTempResponder;

class ListStoresTempAction
{
    public function __construct(StoreTempResponder $responder, ListStoresTempService $service)
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
