<?php

namespace App\Tenant\AppContent\Actions\API;
use App\Tenant\AppContent\Domain\Requests\FaqFormRequest;
use App\Tenant\AppContent\Domain\Services\API\ShowFaqService;
use App\Tenant\AppContent\Responders\API\UpdateFaqResponder;

class ShowFaqAction
{
    public function __construct(UpdateFaqResponder $responder, ShowFaqService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(["faq_id" => $id])
        )->respond();
    }
}
