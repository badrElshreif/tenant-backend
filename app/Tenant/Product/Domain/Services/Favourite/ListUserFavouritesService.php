<?php

namespace App\Tenant\Product\Domain\Services\Favourite;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Symfony\Component\HttpFoundation\Response;

class ListUserFavouritesService extends Service
{
    public function handle($data = [])
    {
        try {
            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            return new GenericPayload(auth()->user()->favourites()->paginate($limit), Response::HTTP_ACCEPTED);

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
