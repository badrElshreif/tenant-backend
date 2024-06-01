<?php

namespace App\Tenant\Product\Domain\Services\Rating;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\Product;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ListProductRatingsService extends Service
{
    public function handle($data = [])
    {
        try {
            $product = Product::findOrFail($data['product_id']);
            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            $ratings = $product->ratings()->where('is_active', 1)->paginate($limit);
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
