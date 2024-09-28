<?php

namespace App\Tenant\AppContent\Actions\ContactUs;
use App\Tenant\AppContent\Domain\Requests\ContactUsFormRequest;
use App\Tenant\AppContent\Domain\Services\ContactUs\UpdateContactUsService;
use App\Tenant\AppContent\Responders\ContactUsResponder;

class UpdateContactUsAction
{
    public function __construct(ContactUsResponder $responder, UpdateContactUsService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(ContactUsFormRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->services->handle(
                array_merge($request->validated(), ["contact_id" => $id])
            )
        )->respond();
    }
}
