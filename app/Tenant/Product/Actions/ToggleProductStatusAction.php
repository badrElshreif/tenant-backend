<?php

namespace App\Tenant\Product\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Product\Domain\Services\ToggleProductStatusService;
use App\Tenant\Product\Domain\Requests\UpdateProductStatusFormRequest;

class ToggleProductStatusAction
{
    public function __construct(GenericResponder $responder, ToggleProductStatusService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(UpdateProductStatusFormRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["product_id" => $id]))
        )->respond();
    }
}
