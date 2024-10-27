<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use Symfony\Component\HttpFoundation\Response;

class ShowBrandService extends Service
{
    public function handle($data = [])
    {
        try {
            $brand = Brand::findOrFail($data['brand_id']);
            
            return new GenericPayload($brand, Response::HTTP_OK
                , ResponseType::SingleResource, BrandResource::class);
        } catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
