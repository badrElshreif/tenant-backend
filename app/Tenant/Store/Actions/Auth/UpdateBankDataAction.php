<?php

namespace App\Tenant\Store\Actions\Auth;
use App\Admin\Domain\Requests\AdminRequest;
use App\Tenant\Store\Domain\Requests\UpdateBankDataRequest;
use App\Tenant\Store\Domain\Services\Auth\UpdateBankDataService;
use App\Admin\Responders\UpdateProfileResponder;

class UpdateBankDataAction
{
    public function __construct(UpdateProfileResponder $responder, UpdateBankDataService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(UpdateBankDataRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
