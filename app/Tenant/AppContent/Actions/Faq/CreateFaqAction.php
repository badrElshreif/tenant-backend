<?php

namespace App\Tenant\AppContent\Actions\API;
use App\Tenant\AppContent\Domain\Services\API\CreateFaqService;
use App\Tenant\AppContent\Responders\API\CreateFaqResponder;
use App\Tenant\AppContent\Domain\Requests\FaqFormRequest;

class CreateFaqAction
{

    private $service, $responder;

    public function __construct(CreateFaqResponder $responder, CreateFaqService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(FaqFormRequest $request)
    {
        return $this->responder->withResponse(
           $this->service->handle($request->validated())
        )->respond();
    }
}
