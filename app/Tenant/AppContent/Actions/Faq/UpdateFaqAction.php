<?php

namespace App\Tenant\AppContent\Actions\API;
use App\Tenant\AppContent\Domain\Requests\FaqFormRequest;
use App\Tenant\AppContent\Domain\Services\API\UpdateFaqService;
use App\Tenant\AppContent\Responders\API\UpdateFaqResponder;

class UpdateFaqAction
{
    public function __construct(UpdateFaqResponder $responder, UpdateFaqService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(FaqFormRequest $request,$id)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(),["faq_id" => $id]))
        )->respond();
    }
}
