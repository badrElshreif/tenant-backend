<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Repositories\BrandRepository;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShowBrandService extends Service
{
    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * Create a new ShowBrandService instance.
     *
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Handle brand retrieval.
     *
     * @param array $data The request data
     * @return array{status: bool, message?: string, data?: BrandResource}
     */
    public function handle(array $data = []): array
    {
        try {
            $brand = $this->brandRepository->findOrFail($data['brand_id']);

            return [
                'status' => true,
                'data' => new BrandResource($brand),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => false,
                'message' => 'Brand not found',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to retrieve brand: ' . $e->getMessage(),
            ];
        }
    }
}
