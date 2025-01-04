<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Category\Domain\Models\Category;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Category\Domain\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;

class UpdateCategoryService extends Service
{
    public function handle($data = [])
    {
        try {
            $category = Category::findOrFail($data['category_id']);
            // if($category->is_active == 1){
            //     if($category->parent_id != null){
            //         if(count($category->products()->get()) > 0)
            //             return new GenericPayload(
            //                  __('error.cannotDeactivate'), 422
            //             );
            //     }else{
            //         if(count($category->childs()->get()) > 0)
            //             return new GenericPayload(
            //                  __('error.cannotDeactivate'), 422
            //             );
            //     }
            // }
            $category->update($data);
            if (isset($data['tax_percentage'])) {
                foreach ($category->products()->get() as $product) {
                    $tax = $product->price * $category->tax_percentage / 100;
                    $product->update([
                        'price_including_tax' => $product->price + $tax
                    ]);
                }
            }
            return [
                'data' => new CategoryResource($category),
                'status' => true,
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
