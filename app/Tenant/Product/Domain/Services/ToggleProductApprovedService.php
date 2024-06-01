<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\Product;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use DB;

class ToggleProductApprovedService extends Service
{
    public function handle($data = [])
    {
        try {
            $product = Product::findOrFail($data['product_id']);

                if(!auth('admin')->check())
                    return new GenericPayload(
                         __('error.cannotDeactivate'), 422
                    );


            // $product->update([
            //     'approved' => !$product->approved,

            // ]);

                    if(isset($data["isActive"]) ){
                        $product->update([
                            'is_active' => !$product->is_active,
                        ]);
                    }else{
                        $product->update([
                            'approved' => !$product->approved,
                        ]);
                    }
            return new GenericPayload($product, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }


    }
}
