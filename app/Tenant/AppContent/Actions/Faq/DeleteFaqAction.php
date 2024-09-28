<?php

namespace App\Tenant\AppContent\Actions\API;
use App\Tenant\AppContent\Domain\Services\API\DeleteFaqService;
use App\Tenant\AppContent\Responders\API\DeleteFaqResponder;

class DeleteFaqAction
{
    public function __construct(DeleteFaqResponder $responder, DeleteFaqService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["faq_id" => $id])
        )->respond();
    }
}
