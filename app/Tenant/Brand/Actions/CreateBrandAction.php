<?php

namespace App\Tenant\Brand\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Brand\Domain\Requests\BrandRequest;
use App\Tenant\Brand\Domain\Services\CreateBrandService;

class CreateBrandAction
{
    public function __construct(GenericResponder $responder, CreateBrandService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(BrandRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
