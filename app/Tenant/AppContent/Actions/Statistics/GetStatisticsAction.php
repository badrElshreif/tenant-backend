<?php

namespace App\Tenant\AppContent\Actions\Statistics;

use App\Tenant\AppContent\Domain\Services\Statistics\GetStatisticsService;
use App\Tenant\AppContent\Responders\SettingResponder;

class GetStatisticsAction
{
    public function __construct(SettingResponder $responder, GetStatisticsService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}
