<?php

namespace App\Tenant\Store\Actions;
use App\Tenant\Store\Domain\Services\ShowStoreService;
use App\Tenant\Store\Responders\StoreResponder;

class ShowStoreAction
{
    public function __construct(StoreResponder $responder, ShowStoreService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["store_id" => $id])
        )->respond();
    }
}
