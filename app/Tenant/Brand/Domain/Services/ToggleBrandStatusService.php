<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Domain\Repositories\BrandRepository;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ToggleBrandStatusService extends Service
{
    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * Create a new ToggleBrandStatusService instance.
     *
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Handle brand status toggle.
     *
     * @param array $data The request data
     * @return array{status: bool, message: string, code?: int, data?: BrandResource}
     */
    public function handle(array $data = []): array
    {
        try {
            $brand = $this->brandRepository->findOrFail($data['brand_id']);

            if ($brand->is_active && $this->hasActiveProducts($brand)) {
                return [
                    'status' => false,
                    'message' => __('error.cannotDeactivate'),
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY
                ];
            }

            $brand = $this->brandRepository->update($brand->id, [
                'is_active' => !$brand->is_active
            ]);

            return [
                'status' => true,
                'message' => __('success.updatedSuccessfully'),
                'data' => new BrandResource($brand)
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => false,
                'message' => 'Brand not found',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle brand status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if the brand has any active products.
     *
     * @param Brand $brand
     * @return bool
     */
    private function hasActiveProducts(Brand $brand): bool
    {
        return $brand->products()
            ->active(1)
            ->count() > 0;
    }
}
