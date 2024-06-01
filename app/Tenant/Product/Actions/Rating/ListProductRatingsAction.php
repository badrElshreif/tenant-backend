<?php

namespace App\Tenant\Product\Actions\Rating;

use App\Tenant\Product\Domain\Requests\RatingFormRequest;
use App\Tenant\Product\Domain\Services\Rating\ListProductRatingsService;
use App\Tenant\Product\Responders\RatingResponder;

class ListProductRatingsAction
{
    public function __construct(RatingResponder $responder, ListProductRatingsService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke(RatingFormRequest $request, $id)
    {
        return $this->responder->withResponse(
            $this->service->handle(array_merge($request->validated(), ["product_id" => $id]))
        )->respond();
    }
}
