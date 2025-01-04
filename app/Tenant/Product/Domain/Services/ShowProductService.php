<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\ProductView;
use App\Tenant\Product\Domain\Resources\ProductResource;
use Symfony\Component\HttpFoundation\Response;

class ShowProductService extends Service
{
    public function handle($data = [])
    {
        try {
            $product = ProductView::findOrFail($data['product_id']);
            return [
                'data' => new ProductResource($product),
                'status' => true,
                'message' => __('success.foundSuccessfully'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }
    }
}
