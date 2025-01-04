<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Responders\BrandResponder;
use Symfony\Component\HttpFoundation\Response;

class ToggleBrandStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $brand = Brand::findOrFail($data['brand_id']);
            if ($brand->is_active) {
                if (count($brand->products()->active(1)->get()) > 0)
                    return [
                        'status' => false,
                        'message' => __('error.cannotDeactivate'),
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY
                    ];
            }
            $brand->update([
                'is_active' => !$brand->is_active
            ]);
            return [
                'status' => true,
                'message' => __('success.updatedSuccessfully'),
                'data' => new BrandResponder($brand)
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
