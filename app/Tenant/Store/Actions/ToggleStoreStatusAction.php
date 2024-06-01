<?php

namespace App\Tenant\Store\Actions;
use App\Tenant\Store\Domain\Requests\ToggleStoreStatusRequest;
use App\Tenant\Store\Domain\Services\ToggleStoreStatusService;
use App\Tenant\Store\Responders\StoreResponder;
use App\Tenant\Store\Domain\Requests\StoreFormRequest;

class ToggleStoreStatusAction
{
    public function __construct(StoreResponder $responder, ToggleStoreStatusService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(ToggleStoreStatusRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["store_id" => $id]))
        )->respond();
    }
}
