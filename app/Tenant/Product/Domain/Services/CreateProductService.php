<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Infrastructure\Traits\UploaderHelper;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Product\Domain\Models\Product;
use App\Tenant\Product\Domain\Resources\ProductResource;
use App\Tenant\Property\Domain\Models\Property;
use App\Tenant\Property\Domain\Models\PropertyOption;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreateProductService extends Service
{
    use UploaderHelper;

    public function handle($data = [])
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


            $data['image'] = $this->handleUploadImg($data['image'],'products');

            $product = Product::create($data);


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
            return new GenericPayload($product, Response::HTTP_OK,
                ResponseType::SingleResource, ProductResource::class);

        } catch (\Illuminate\Database\QueryException $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        } catch (\PDOException $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        } catch (\Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage() . "- Line " . $ex->getFile() . ":" . $ex->getLine(), 422
            );
        }

    }

    private function saveProperties($product, $properties)
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
