<?php

namespace App\Tenant\Brand\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Brand\Domain\Services\ToggleBrandStatusService;
use App\Tenant\Brand\Responders\BrandResponder;

class ToggleBrandStatusAction
{
    public function __construct(GenericResponder $responder, ToggleBrandStatusService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke($id)
    {
        return $this->responder->withResponse(
            $this->services->handle(["brand_id" => $id])
        )->getResponseData();
    }
}
