<?php

namespace App\Tenant\Product\Actions\Rating;

use App\Tenant\Product\Domain\Requests\RatingFormRequest;
use App\Tenant\Product\Domain\Services\Rating\ListCommentsService;
use App\Tenant\Product\Responders\RatingResponder;

class ListCommentsAction
{
    public function __construct(RatingResponder $responder, ListCommentsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(RatingFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->service->handle($request->validated())
        )->respond();
    }
}
