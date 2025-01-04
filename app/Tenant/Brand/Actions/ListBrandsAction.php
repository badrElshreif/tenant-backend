<?php

namespace App\Tenant\Brand\Actions;
use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Brand\Domain\Requests\BrandRequest;
use App\Tenant\Brand\Domain\Services\ListBrandsService;

class ListBrandsAction
{
    public function __construct(GenericResponder $responder, ListBrandsService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(BrandRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request)
        )->respond();
    }
}
