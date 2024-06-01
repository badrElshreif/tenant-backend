<?php

namespace App\Tenant\Store\Actions\Auth;
use App\Admin\Domain\Requests\AdminRequest;
use App\Tenant\Store\Domain\Services\Auth\UpdateProfileService;
use App\Admin\Responders\UpdateProfileResponder;

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
