<?php

namespace App\Tenant\AppContent\Actions\API;

use App\Tenant\AppContent\Domain\Services\API\ListFaqsService;
use App\Tenant\AppContent\Responders\API\ListFaqsResponder;

class ListFaqsAction
{
    public function __construct(ListFaqsResponder $responder, ListFaqsService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}
