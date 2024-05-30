<?php

namespace App\Main\Tenant\Actions;

use App\Main\Tenant\Domain\Requests\CreateTenantFormRequest;
use App\Main\Tenant\Domain\Services\CreateTenantService;
use App\Main\Tenant\Responders\TenantResponder;

class CreateTenantAction
{
    public function __construct(TenantResponder $responder, CreateTenantService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(CreateTenantFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
