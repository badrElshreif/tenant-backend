<?php

namespace App\Tenant\Product\Actions\Rating;

use App\Tenant\Product\Domain\Requests\RatingFormRequest;
use App\Tenant\Product\Domain\Services\Rating\AddRateService;
use App\Tenant\Product\Responders\RatingResponder;

class AddRateAction
{
    public function __construct(RatingResponder $responder, AddRateService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(RatingFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
