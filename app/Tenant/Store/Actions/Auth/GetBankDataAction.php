<?php

namespace App\Tenant\Store\Actions\Auth;
use App\Tenant\Store\Domain\Services\Auth\GetBankDataService;
use App\Tenant\Store\Responders\StoreResponder;
use Illuminate\Support\Facades\Request;

class GetBankDataAction
{
    public function __construct(StoreResponder $responder, GetBankDataService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(Request $request)
    {
        return $this->responder->withResponse(
            $this->services->handle([])
        )->respond();
    }
}
