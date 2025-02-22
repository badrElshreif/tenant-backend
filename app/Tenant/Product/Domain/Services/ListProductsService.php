<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Repositories\ProductRepository;
use App\Tenant\Product\Domain\Resources\ProductLiteResource;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ListProductsService extends Service
{

    protected ProductRepository $productRepository;

    /**
     * Create a new ListProductsService instance.
     *
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Handle the listing of products with filters and pagination.
     *
     * @param array $data
     * @return array
     */
    public function handle(array $data = []): array
    {
        try {

            if(auth()->check()){
                $data['latitude'] = auth()->user()->latitude;
                $data['longitude'] = auth()->user()->longitude;
            }

            $query = $this->productRepository->filter($data);


            if (isset($data['is_paginated'])) {
                $products = $query->paginate($data['per_page'] ?? config('app.pagination_limit'));

                return [
                    'data' => ProductLiteResource::listCollection($products),
                    'status' => true,
                    'message' => __('success.listedSuccessfully'),
                ];
            }else{
                $products = $query->get();

                return [
                    'data' => ProductLiteResource::collection($products),
                    'status' => true,
                    'message' => __('success.listedSuccessfully'),
                ];
            }

        } catch (\Exception $e) {
            Log::error('List products error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }
    }
}

