<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;

class DeleteBrandService extends Service
{
    public function handle($data = [])
    {
        try {
            $brand = Brand::findOrFail($data['brand_id']);
            if (count($brand->products()->get()) > 0)
                return new GenericPayload(
                    __('error.cannotDelete'), 422
                );
            $brand->delete();

            return [
                'status' => true,
                'message' => __('success.deletedSuccessfuly'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
}
