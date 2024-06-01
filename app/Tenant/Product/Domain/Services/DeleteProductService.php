<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\Product;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use DB;

class DeleteProductService extends Service
{
    public function handle($data = [])
    {
        try {
            $product = Product::findOrFail($data['product_id']);
          //   if(count($product->orders()->get()) > 0)
        		// return new GenericPayload(
          //           __('error.cannotDelete'), 422
          //       );

            if($product->category->type == 'stores' && count($product->orders()->get()) > 0)
                    return new GenericPayload(
                         __('error.cannotDelete'), 422
                    );

                if($product->category->type == 'centers' && count($product->serviceOrders()->get()) > 0)
                    return new GenericPayload(
                         __('error.cannotDelete'), 422
                    );

            $product->attachments()->delete();
            //dd($product->properties);
            if(count($product->properties) > 0)
                $product->properties()->detach();

            //$product->properties()->delete();
            $product->delete();

            DB::table('carts')->where('product_id', $data['product_id'])->delete();
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
