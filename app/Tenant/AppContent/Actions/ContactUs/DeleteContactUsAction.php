<?php

namespace App\Tenant\AppContent\Actions\ContactUs;
use App\Tenant\AppContent\Domain\Services\ContactUs\DeleteContactUsService;
use App\Tenant\AppContent\Responders\ContactUsResponder;

class DeleteContactUsAction
{
    public function __construct(ContactUsResponder $responder, DeleteContactUsService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["contact_id" => $id])
        )->respond();
    }
}
