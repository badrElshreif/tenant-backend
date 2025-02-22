<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Traits\UploaderHelper;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Product\Domain\Models\Product;
use App\Tenant\Product\Domain\Repositories\ProductRepository;
use App\Tenant\Product\Domain\Resources\ProductResource;
use App\Tenant\Property\Domain\Models\Property;
use App\Tenant\Property\Domain\Models\PropertyOption;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreateProductService extends Service
{
    use UploaderHelper;

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * Create a new CreateProductService instance.
     *
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Handle product creation.
     *
     * @param array $data The request data
     * @return array{status: bool, message: string, data?: ProductResource, code?: int}
     */
    public function handle(array $data = []): array
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            $data['key'] = rand(111111, 99999) . time();
            $data['created_by'] = auth()->id();
            $data['store_id'] = (auth('tenant-store')->check() &&
                auth('tenant-store')->user()->store) ?
                auth('tenant-store')->user()->store->id : $data['store_id'] ?? null;

            if (!isset($data['store_id']))
                //  return new GenericPayload(__('error.requiredStore'), 422);

                $data['approved'] = auth('tenant-admin')->check();

            $category = Category::findOrFail($data['category_id']);
            if (!$category->parent && $category->type == 'stores')
                // return new GenericPayload(__('error.requiredSubCategory'), 422);

                if ($category->type == 'stores') {
                    $properties = $category->parent?->properties()->where('is_required', 1)->get();
                    if ($properties && count($properties) > 0 && !isset($data['properties']))
                        return new GenericPayload(__('error.requiredProperties'), 422);
                }


            $data['image'] = $this->handleUploadImg($data['image'], 'products');

            $product = $this->productRepository->create($data);


            if (isset($data['attachments'])) {
                //$product->attachments()->createMany($data['attachments']);
                $attachments = [];
                foreach ($data['attachments'] as $attachment) {
                    $attachments[] = array_merge($attachment,
                        [
                            'attachable_id' => $product->id,
                            'attachable_type' => Product::class,
                        ]);
                }
                auth()->user()->attachments()->createMany($attachments);
            }

            if (isset($data['properties']))
                $this->saveProperties($product, $data['properties']);

            // Commit Transaction
            DB::commit();
            return [
                'data' => new ProductResource($product),
                'status' => true,
                'message' => __('success.createdSuccessfully'),
            ];

        } catch (\Exception $e) {
            DB::rollback();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }

    }

    /**
     * Save product properties.
     *
     * @param Product $product
     * @param array $properties
     * @return void
     */
    private function saveProperties(Product $product, array $properties): void
    {
        $properties_arr = [];
        foreach ($properties as $property) {
            if (isset($property['value']) && $property['value'] != '') {
                $prop = Property::whereId($property['property_id'])->firstOrFail();
                $property['property_option_id'] = null;
                if ($prop->propertyType->has_options == 1) {
                    $option = PropertyOption::findOrFail($property['value']);
                    $property['property_option_id'] = $option->id;
                    $property['value'] = null;
                }
                $properties_arr[] = [
                    'property_id' => $property['property_id'],
                    'value' => $property['value'],
                    'property_option_id' => $property['property_option_id']
                ];
            }
        }
        //dd($properties_arr);
        $product->properties()->sync($properties_arr);
    }
}
