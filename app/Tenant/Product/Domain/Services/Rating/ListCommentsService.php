<?php

namespace App\Tenant\Product\Domain\Services\Rating;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\Rating;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Product\Domain\Filters\RatingFilter;
use Symfony\Component\HttpFoundation\Response;

class ListCommentsService extends Service
{
    protected $filter;

    public function __construct(RatingFilter $filter)
    {
        $this->filter = $filter;
    }
    public function handle($data = [])
    {
        try {
            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            $ratings = Rating::filter($this->filter)->paginate($limit);
            return new GenericPayload($ratings, Response::HTTP_ACCEPTED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\PDOException $ex){
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
