<?php

namespace App\Tenant\AppContent\Actions\ContactUs;
use App\Tenant\AppContent\Domain\Requests\ContactUsFormRequest;
use App\Tenant\AppContent\Domain\Services\ContactUs\ShowContactUsService;
use App\Tenant\AppContent\Responders\ContactUsResponder;

class ShowContactUsAction
{
    public function __construct(ContactUsResponder $responder, ShowContactUsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->service->handle(
                ["contact_id" => $id]
            )
        )->respond();
    }
}
