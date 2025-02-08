<?php

namespace App\User\Actions\User;

use App\Infrastructure\Responders\GenericResponder;
use App\Store\Domain\Requests\SellerChangePasswordRequest;
use App\User\Domain\Services\User\AdminChangePasswordService;

class AdminUserChangePasswordAction
{

    public function __construct(GenericResponder $responder, AdminChangePasswordService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($id, SellerChangePasswordRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["id" => $id]))
        )->respond();
    }


}
