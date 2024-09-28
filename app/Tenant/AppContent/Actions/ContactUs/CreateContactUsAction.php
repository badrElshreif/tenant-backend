<?php

namespace App\Tenant\AppContent\Actions\ContactUs;
use App\Tenant\AppContent\Domain\Services\ContactUs\CreateContactUsService;
use App\Tenant\AppContent\Responders\ContactUsResponder;
use App\Tenant\AppContent\Domain\Requests\ContactUsFormRequest;

class CreateContactUsAction
{

    public function __construct(ContactUsResponder $responder, CreateContactUsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

     public function __invoke(ContactUsFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
