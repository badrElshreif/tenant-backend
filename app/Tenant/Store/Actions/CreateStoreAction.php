<?php

namespace App\Tenant\Store\Actions;

use App\Tenant\Store\Domain\Requests\StoreFormRequest;
use App\Tenant\Store\Domain\Services\CreateStoreService;
use App\Tenant\Store\Responders\StoreResponder;

class CreateStoreAction
{
    public function __construct(StoreResponder $responder, CreateStoreService $service)
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
