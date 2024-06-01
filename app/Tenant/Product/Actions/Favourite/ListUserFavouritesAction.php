<?php

namespace App\Tenant\Product\Actions\Favourite;
use App\Tenant\Product\Domain\Requests\FavouriteFormRequest;
use App\Tenant\Product\Domain\Services\Favourite\ListUserFavouritesService;
use App\Tenant\Product\Responders\ProductResponder;

class ListUserFavouritesAction
{
    public function __construct(ProductResponder $responder, ListUserFavouritesService $services)
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
