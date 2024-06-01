<?php

namespace App\Tenant\Store\Actions;

use App\Tenant\Store\Domain\Requests\SetFeaturedStoresRequest;
use App\Tenant\Store\Domain\Services\SetFeaturedStoresService;
use App\Tenant\Store\Responders\StoreResponder;

class SetFeaturedStoresAction
{
    public function __construct(StoreResponder $responder, SetFeaturedStoresService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }
    public function __invoke(SetFeaturedStoresRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
