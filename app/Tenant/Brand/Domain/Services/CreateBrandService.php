<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Responders\BrandResponder;

class CreateBrandService extends Service
{
    public function handle($data = [])
    {
        $data['is_active'] = isset($data['is_active']) ? $data['is_active'] : 1;
        $data['image'] = (new Brand)->handleUploadImg($data['image']);
        $brand = Brand::create($data);
        return [
            'status' => true,
            'message' => 'Brand created successfully',
            'data' => new BrandResponder($brand),
        ];
    }
}
