<?php

namespace App\Tenant\Product\Domain\Services\Favourite;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Symfony\Component\HttpFoundation\Response;

class AddProductToFavouriteService extends Service
{
    public function handle($data = [])
    {
        try {
            $user = auth()->user();
            //return new GenericPayload($user->favourites->pluck('id')->toArray());
            if(in_array($data['product_id'], $user->favourites->pluck('id')->toArray()))
                return new GenericPayload(
                __('error.productAddedBefore'), 422
            );
            $user->favourites()->attach($data['product_id']);
            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            return new GenericPayload($user->favourites()->paginate($limit), Response::HTTP_ACCEPTED);

        } catch (\PDOException $ex){
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
