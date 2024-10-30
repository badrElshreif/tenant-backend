<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Product\Domain\Models\Product;
use App\Tenant\Product\Domain\Models\ProductView;
use App\Tenant\Product\Domain\Resources\ProductResource;
use Symfony\Component\HttpFoundation\Response;

class ShowProductService extends Service
{
    public function handle($data = [])
    {
        try {
            $product = ProductView::findOrFail($data['product_id']);
            return new GenericPayload($product, Response::HTTP_OK,
                ResponseType::SingleResource, ProductResource::class);
        } catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
