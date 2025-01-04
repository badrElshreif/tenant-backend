<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Responders\BrandResponder;

class UpdateBrandService extends Service
{
    public function handle($data = [])
    {
        try {
            $brand = Brand::findOrFail($data['brand_id']);
            if (!empty($data['image'])) {
                $data['image'] = (new Brand)->handleUploadImg($data['image']);
            }

            $brand->update($data);
            if (isset($data['tax_percentage'])) {
                foreach ($brand->products()->get() as $product) {
                    $tax = $product->price * $brand->tax_percentage / 100;
                    $product->update([
                        'price_including_tax' => $product->price + $tax
                    ]);
                }
            }
            return [
                'status' => true,
                'message' => 'Brand updated successfully',
                'data' => new BrandResponder($brand),
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }


    }
}
