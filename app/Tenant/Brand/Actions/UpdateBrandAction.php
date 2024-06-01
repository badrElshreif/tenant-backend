<?php

namespace App\Tenant\Brand\Actions;
use App\Tenant\Brand\Domain\Requests\BrandRequest;
use App\Tenant\Brand\Domain\Services\UpdateBrandService;
use App\Tenant\Brand\Responders\BrandResponder;

class UpdateBrandAction
{
    public function __construct(BrandResponder $responder, UpdateBrandService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(BrandRequest $request, $id)
    {

        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["brand_id" => $id]))
        )->respond();
    }
}
