<?php

namespace App\Tenant\Location\Actions;
use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Location\Domain\Services\DeleteCountryService;

class DeleteCountryAction
{
    public function __construct(GenericResponder $responder, DeleteCountryService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["country_id" => $id])
        )->respond();
    }
}
