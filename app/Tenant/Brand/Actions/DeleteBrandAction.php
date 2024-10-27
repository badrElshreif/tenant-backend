<?php

namespace App\Tenant\Brand\Actions;
use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Brand\Domain\Services\DeleteBrandService;
use App\Tenant\Brand\Responders\BrandResponder;

class DeleteBrandAction
{
    public function __construct(GenericResponder $responder, DeleteBrandService $services)
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
