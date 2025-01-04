<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use DB;

class ToggleProductStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $product = Product::findOrFail($data['product_id']);
            if ($product->is_active == 1) {
                if ($product->category->type == 'stores' && count($product->orders()->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );

                if ($product->category->type == 'centers' && count($product->serviceOrders()->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );

                // if(isset($data['deactivation_start_date']) && isset($data['deactivation_end_date'])){
                //     $product->update([
                //         'deactivation_start_date' => $data['deactivation_start_date'],
                //         'deactivation_end_date' => $data['deactivation_end_date'],
                //     ]);
                //     return new GenericPayload($product);
                // }else if($product->deactivation_start_date!==null && $product->deactivation_end_date!== null && $product->deactivation_start_date <= date('Y-m-d') && $product->deactivation_end_date >= date('Y-m-d')){
                //         $product->update([
                //             'is_active' => 1,
                //             'deactivation_start_date' => null,
                //             'deactivation_end_date' => null,
                //         ]);
                //         return new GenericPayload($product, Response::HTTP_CREATED);
                // }

                DB::table('carts')->where('product_id', $data['product_id'])->delete();

            }

            $product->update([
                'is_active' => !$product->is_active,
                'deactivation_start_date' => null,
                'deactivation_end_date' => null,
            ]);
            return [
                'status' => true,
                'data' => $product,
                'message' => __('success.updatedSuccessfully'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }


    }
}
