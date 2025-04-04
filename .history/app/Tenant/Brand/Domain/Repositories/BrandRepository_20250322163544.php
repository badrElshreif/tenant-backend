<?php

namespace App\Tenant\Brand\Domain\Repositories;

use App\Infrastructure\Domain\Repositories\Repository;
use App\Tenant\Brand\Domain\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandRepository extends Repository
{
    private const DEFAULT_ORDER_COLUMN = 'id';
    private const DEFAULT_ORDER_TYPE = 'DESC';
    private const ACTIVE_STATUS = 1;

    /**
     * Create a new BrandRepository instance.
     *
     * @param Brand $brand
     */
    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
    }

    /**
     * Create a new brand with translations.
     *
     * @param array $data
     * @return Brand
     */
    public function create(array $data): Brand
    {
        // Handle image upload if it's a file object
        $imagePath = null;
        if (isset($data['image']) && is_object($data['image'])) {
            // Get tenant slug for tenant-specific storage
            $tenantSlug = getTenant()->slug;

            // Store in tenant-specific directory
            $imagePath = $data['image']->store($tenantSlug . '/brands', 'public');
        } elseif (isset($data['image']) && is_string($data['image'])) {
            $imagePath = $data['image'];
        }

        // Create the brand with non-translatable attributes
        $brand = $this->model->create([
            'image' => $imagePath,
            'is_active' => $data['is_active'] ?? self::ACTIVE_STATUS,
        ]);

        // Define supported languages
        $languages = ['en', 'ar'];

        // Loop through languages and save translations
        foreach ($languages as $lang) {
            if (isset($data[$lang])) {
                $brand->translateOrNew($lang)->name = $data[$lang]['name'] ?? '';
                $brand->translateOrNew($lang)->description = $data[$lang]['description'] ?? '';
            }
        }

        // Save the translations
        $brand->save();

        return $brand;
    }

    /**
     * Build query with filters and sorting
     *
     * @param array $request
     * @return self
     */
    public function filter($request): self
    {
        $orderColumn = $request['order_by'] ?? self::DEFAULT_ORDER_COLUMN;
        $orderType = $request['order_type'] ?? self::DEFAULT_ORDER_TYPE;

        $this->model = $this->model
            ->when(
                isset($request['active']),
                fn (Builder $query) => $query->where('brands.is_active', (bool)$request['active'])
            )
            ->when(
                isset($request['search']),
                fn (Builder $query) => $this->searchByName($query, $request['search'])
            )
            ->when(
                $orderColumn === 'name',
                fn (Builder $query) => $this->orderByTranslatedName($query, $orderType),
                fn (Builder $query) => $query->orderBy("brands.".$orderColumn, $orderType)
            );

        return $this;
    }

    /**
     * Order query by translated name
     *
     * @param Builder $query
     * @param string $orderType
     * @return Builder
     */
    private function orderByTranslatedName(Builder $query, string $orderType): Builder
    {
        return $query->join('brand_translations', function ($join) {
                $join->on('brands.id', '=', 'brand_translations.brand_id')
                    ->where('brand_translations.locale', '=', app()->getLocale());
            })
            ->groupBy('brands.id', 'brand_translations.id')
            ->orderBy('brand_translations.name', $orderType)
            ->select('brands.*', 'brand_translations.id as brand_translation_id');
    }

    private function searchByName(Builder $query, string $search): Builder
    {
        return $query->join('brand_translations', function ($join) use ($search) {
            $join->on('brands.id', '=', 'brand_translations.brand_id')
                ->where('brand_translations.locale', '=', app()->getLocale())
                ->where('brand_translations.name', 'like', "%{$search}%");
        });
    }

    /**
     * Order brands by specified column and direction
     *
     * @param string|null $column Column to order by
     * @param string|null $direction Direction to order (ASC or DESC)
     * @return self
     */
    public function orderBy(?string $column = null, ?string $direction = null): self
    {
        $column = $column ?? self::DEFAULT_ORDER_COLUMN;
        $direction = $direction ?? self::DEFAULT_ORDER_TYPE;

        // Validate direction
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        // Handle special case for ordering by name (which is in translations table)
        if ($column === 'name') {
            $this->model = $this->orderByTranslatedName($this->model->query(), $direction);
        } else {
            // For other columns, use standard ordering
            $this->model = $this->model->orderBy("brands.{$column}", $direction);
        }

        return $this;
    }

    /**
     * Delete a brand and clean up associated resources
     *
     * @param int $id Brand ID to delete
     * @return bool True if successful, false otherwise
     */
    public function destroy(int $id): bool
    {
        try {
            // Find the brand
            $brand = $this->model->findOrFail($id);

            // Delete the brand image if it exists
            if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }

            // Delete the brand (this will cascade delete translations due to foreign key constraints)
            return $brand->delete();
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to delete brand: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle the active status of a brand
     *
     * @param int $id Brand ID to toggle status
     * @return Brand|null The updated brand or null if operation failed
     */
    public function toggleStatus(int $id): ?Brand
    {
        try {
            // Find the brand
            $brand = $this->model->findOrFail($id);
            
            // Toggle the is_active status
            $brand->is_active = !$brand->is_active;
            
            // Save the changes
            $brand->save();
            
            return $brand;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to toggle brand status: ' . $e->getMessage());
            return null;
        }
    }

}
