<?php

namespace App\Tenant\Product\Actions\Favourite;

use App\Tenant\Product\Domain\Requests\FavouriteFormRequest;
use App\Tenant\Product\Domain\Services\Favourite\DeleteProductFromFavouriteService;
use App\Tenant\Product\Responders\ProductResponder;

class DeleteProductFromFavouriteAction
{
    public function __construct(ProductResponder $responder, DeleteProductFromFavouriteService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(FavouriteFormRequest $request)
    {
        return $this->responder->withResponse(
            $this->services->handle($request->validated())
        )->respond();
    }
}
