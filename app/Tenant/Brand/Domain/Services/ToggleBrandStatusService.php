<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ToggleBrandStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $brand = Brand::findOrFail($data['brand_id']);
            if($brand->is_active){
                if(count($brand->products()->active(1)->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );
            }
            $brand->update([
                'is_active' => !$brand->is_active
            ]);
            return new GenericPayload($brand, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
