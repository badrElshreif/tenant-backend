<?php

namespace App\Tenant\Product\Domain\Services\Favourite;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Symfony\Component\HttpFoundation\Response;

class DeleteProductFromFavouriteService extends Service
{
    public function handle($data = [])
    {
        try {
            $user = auth()->user();
            if(!in_array($data['product_id'], $user->favourites->pluck('id')->toArray()))
                return new GenericPayload(
                __('error.FavouriteNotFound'), 422
            );
            $user->favourites()->detach($data['product_id']);
            return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);
            // $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            // return new GenericPayload($user->favourites()->paginate($limit));

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
