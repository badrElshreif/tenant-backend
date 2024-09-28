<?php

namespace App\Tenant\AppContent\Actions\API;
use App\Tenant\AppContent\Domain\Services\API\SendPromotionMailService;
use App\Tenant\AppContent\Responders\API\SendPromotionMailResponder;
use App\Tenant\AppContent\Domain\Requests\SendPromotionMailFormRequest;

class SendPromotionMailAction
{

    private $service, $responder;

    public function __construct(SendPromotionMailResponder $responder, SendPromotionMailService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(SendPromotionMailFormRequest $request)
    {
        return $this->responder->withResponse(
           $this->service->handle($request->validated())
        )->respond();
    }
}
