<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use Symfony\Component\HttpFoundation\Response;

class CreateBrandService extends Service
{
    public function handle($data = [])
    {
        $data['is_active'] = isset($data['is_active']) ? $data['is_active'] : 1;
        $data['image'] = (new Brand)->handleUploadImg($data['image']);
        $brand = Brand::create($data);
        return new GenericPayload($brand, Response::HTTP_CREATED);

    }
}
