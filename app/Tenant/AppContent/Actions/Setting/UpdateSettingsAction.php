<?php

namespace App\Tenant\AppContent\Actions\Setting;
use App\Tenant\AppContent\Domain\Requests\SettingRequest;
use App\Tenant\AppContent\Domain\Services\Setting\UpdateSettingsService;
use App\Tenant\AppContent\Responders\SettingResponder;

class UpdateSettingsAction
{
    public function __construct(SettingResponder $responder, UpdateSettingsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(SettingRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
