<?php

namespace App\Tenant\Store\Actions;
use App\Tenant\Store\Domain\Requests\ProcessJoinStoreRequest;
use App\Tenant\Store\Domain\Services\ProcessJoinStoresService;
use App\Tenant\Store\Responders\StoreResponder;

class ProcessJoinStoresAction
{
    public function __construct(StoreResponder $responder, ProcessJoinStoresService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ProcessJoinStoreRequest $request,$id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["store_id" => $id]))
        )->respond();
    }
}
