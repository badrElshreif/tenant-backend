<?php

namespace App\Infrastructure\Collections;

use App\Infrastructure\Traits\ApiPaginator;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomJsonResource extends JsonResource
{

    use ApiPaginator;

    public static function listCollection($resource)
    {
        $collection = (self::collection($resource));
        if (request()->expectsJson()) {
            return self::getPaginatedResponseStatic(
                $collection->resource,
                $collection,
            );
        }

        return $collection;
    }
}
