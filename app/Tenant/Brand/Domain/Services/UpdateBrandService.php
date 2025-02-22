<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Domain\Repositories\BrandRepository;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateBrandService extends Service
{
    private const TAX_PERCENTAGE_DIVISOR = 100;

    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * Create a new UpdateBrandService instance.
     *
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Handle brand update.
     *
     * @param array $data The brand update data
     * @return array{status: bool, message: string, data?: BrandResource}
     */
    public function handle(array $data = []): array
    {
        try {
            $brand = $this->updateBrand($data);

            return [
                'status' => true,
                'message' => 'Brand updated successfully',
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
                'message' => 'Failed to update brand: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update the brand and its related data.
     *
     * @param array $data
     * @return Brand
     */
    private function updateBrand(array $data): Brand
    {
        $brand = $this->brandRepository->findOrFail($data['brand_id']);

        if (!empty($data['image'])) {
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        $brand = $this->brandRepository->update($brand->id, $data);

        if (isset($data['tax_percentage'])) {
            $this->updateProductPrices($brand);
        }

        return $brand;
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

    /**
     * Update prices for all products of the brand.
     *
     * @param Brand $brand
     * @return void
     */
    private function updateProductPrices(Brand $brand): void
    {
        $brand->products()->get()->each(function ($product) use ($brand) {
            $tax = $this->calculateTax($product->price, $brand->tax_percentage);
            $product->update([
                'price_including_tax' => $product->price + $tax
            ]);
        });
    }

    /**
     * Calculate tax amount.
     *
     * @param float $price
     * @param float $taxPercentage
     * @return float
     */
    private function calculateTax(float $price, float $taxPercentage): float
    {
        return $price * $taxPercentage / self::TAX_PERCENTAGE_DIVISOR;
    }
}
