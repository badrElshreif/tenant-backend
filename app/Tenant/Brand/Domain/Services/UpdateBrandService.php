<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class UpdateBrandService extends Service
{
    public function handle($data = [])
    {
        try {
            $brand = Brand::findOrFail($data['brand_id']);
            if(!empty($data['image'])){
                $data['image'] = (new Brand)->handleUploadImg($data['image']);
            }

            $brand->update($data);
            if(isset($data['tax_percentage'])){
                foreach($brand->products()->get() as $product){
                    $tax = $product->price * $brand->tax_percentage / 100;
                    $product->update([
                        'price_including_tax' => $product->price + $tax
                    ]);
                }
            }
            return new GenericPayload($brand, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong')." ".$ex->getMessage(), 422
            );
        }


    }
}
