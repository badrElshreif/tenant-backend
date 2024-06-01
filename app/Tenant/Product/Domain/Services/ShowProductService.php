<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\Product;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Product\Domain\Models\ProductView;
use Symfony\Component\HttpFoundation\Response;

class ShowProductService extends Service
{
    public function handle($data = [])
    {
        try {
            $product = ProductView::findOrFail($data['product_id']);
            return new GenericPayload($product, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
