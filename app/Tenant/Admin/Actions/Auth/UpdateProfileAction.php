<?php

namespace App\Tenant\Admin\Actions\Auth;
use App\Tenant\Admin\Domain\Requests\AdminRequest;
use App\Tenant\Admin\Domain\Services\Auth\UpdateProfileService;
use App\Tenant\Admin\Responders\UpdateProfileResponder;

class UpdateProfileAction
{
    public function __construct(UpdateProfileResponder $responder, UpdateProfileService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(AdminRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
