<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class DeleteBrandService extends Service
{
    public function handle($data = [])
    {
        try {
            $brand = Brand::findOrFail($data['brand_id']);
            if(count($brand->products()->get()) > 0)
                return new GenericPayload(
                    __('error.cannotDelete'), 422
                );
            $brand->delete();
            return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

    }
}
