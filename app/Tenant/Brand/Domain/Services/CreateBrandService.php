<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Domain\Repositories\BrandRepository;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use Exception;

class CreateBrandService extends Service
{
    private const DEFAULT_ACTIVE_STATUS = 1;

    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * Create a new CreateBrandService instance.
     *
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Handle brand creation.
     *
     * @param array $data The brand data
     * @return array{status: bool, message: string, data?: BrandResource}
     */
    public function handle(array $data = []): array
    {
        try {
            $brand = $this->brandRepository->create(
                $this->prepareData($data)
            );

            return [
                'status' => true,
                'message' => 'Brand created successfully',
                'data' => new BrandResource($brand),
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create brand: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Prepare brand data for creation.
     *
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        return [
            ...($data),
            'is_active' => $data['is_active'] ?? self::DEFAULT_ACTIVE_STATUS,
            'image' => $this->handleImageUpload($data['image']),
        ];
    }

    /**
     * Handle image upload for the brand.
     *
     * @param mixed $image
     * @return string
     */
    private function handleImageUpload($image): string
    {
        return (new Brand)->handleUploadImg($image);
    }
}
