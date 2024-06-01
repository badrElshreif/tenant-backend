<?php

namespace App\Tenant\Store\Actions;
use App\Tenant\Store\Domain\Requests\StoreFormRequest;
use App\Tenant\Store\Domain\Services\UpdateStoreService;
use App\Tenant\Store\Responders\StoreResponder;;

class UpdateStoreAction
{
    public function __construct(StoreResponder $responder, UpdateStoreService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(StoreFormRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["store_id" => $id]))
        )->respond();
    }
}
