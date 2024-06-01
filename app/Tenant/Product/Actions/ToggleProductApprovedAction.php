<?php

namespace App\Tenant\Product\Actions;
use App\Tenant\Product\Domain\Requests\UpdateProductApprovedFormRequest;
use App\Tenant\Product\Domain\Services\ToggleProductApprovedService;
use App\Tenant\Product\Responders\ProductResponder;

class ToggleProductApprovedAction
{
    public function __construct(ProductResponder $responder, ToggleProductApprovedService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(UpdateProductApprovedFormRequest $request, $id)
    {
        $isActive = null ;
        if(isset($request[0]['is_active'])){
            $isActive = $request[0]['is_active'];
        }
        return $this->responder->withResponse(
            // $this->services->handle(array_merge($request->validated(), ["product_id" => $id]))
            $this->services->handle(array_merge($request->validated() , ["product_id" => $id , "isActive" => $isActive ]))
        )->respond();
    }
}
