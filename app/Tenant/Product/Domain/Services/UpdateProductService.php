<?php

namespace App\Tenant\Product\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Models\Product;
use App\Category\Domain\Models\Category;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Property\Domain\Models\Property;
use Symfony\Component\HttpFoundation\Response;

class UpdateProductService extends Service
{
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            $product = Product::findOrFail($data['product_id']);
            if(isset($data['category_id'])){
                $category = Category::findOrFail($data['category_id']);
            }else{
                $category = $product->category;
            }
                $data['approved']=auth('admin')->check();

            // if(isset($data['is_active']) && $data['is_active'] == 0){
            // 	if(count($product->orders()->get()) > 0)
            // 		return new GenericPayload(
            //              __('error.cannotDeactivate'), 422
            //         );
            // }
            $product->update($data);

            if(isset($data['attachments']))
                $this->updateAttachments($product, $data['attachments']);

            if(isset($data['properties']) && $category->type == 'stores')
                $this->saveProperties($product, $data['properties']);
        // Commit Transaction
            DB::commit();
            return new GenericPayload($product, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\PDOException $ex){
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }catch (Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }

    private function updateAttachments($product, $attachments=[]){
        $attachment_ids = array_column($attachments, 'id');
        foreach($product->attachments as $attachment){
            if(! in_array($attachment->id, $attachment_ids))
                $attachment->delete();
        }
        foreach($attachments as $attachment){
            $product->attachments()->updateOrCreate(
                [
                    'id' => $attachment['id']?? null
                ],
                $attachment
            );
        }

        return null;
    }

    private function saveProperties($product, $properties){
        $product->properties()->detach();
        $properties_arr = [];
        foreach ($properties as $property) {
            if(isset($property['value']) && $property['value'] != ''){
                //dd($property['property_id']);
                //$prop = Property::where('id', $property['property_id'])->firstOrFail();
                $prop = Property::findOrFail($property['property_id']);
                //dd($prop);
                $property['property_option_id'] = null;
                if($prop->propertyType->has_options == 1){
                    $option = \App\Property\Domain\Models\PropertyOption::findOrFail($property['value']);
                    $property['property_option_id'] = $option->id;
                    $property['value'] = null;
                }
                array_push($properties_arr, [
                    'property_id' => $property['property_id'],
                    'value' => $property['value'],
                    'property_option_id' => $property['property_option_id']
                ]);
            }
        }
        $product->properties()->sync($properties_arr);
    }


    // private function saveProperties($product, $properties){
    //     // $properties_ids = array_column($properties, 'id');
    //     // foreach($product->properties as $prop){
    //     //     if(! in_array($prop->id, $properties_ids))
    //     //         $prop->delete();
    //     // }
    //     $properties_arr = [];
    //     foreach ($properties as $property) {
    //         //dd($property['property_id']);
    //         if(isset($property['value']) && $property['value'] != ''){
    //             $prop = Property::findOrFail($property['property_id']);
    //             $property['property_option_id'] = null;
    //             if($prop->propertyType->has_options == 1){
    //                 $option = \App\Property\Domain\Models\PropertyOption::findOrFail($property['value']);
    //                 $property['property_option_id'] = $option->id;
    //                 $property['value'] = null;
    //             }
    //             array_push($properties_arr, [
    //                 'property_id' => $property['property_id'],
    //                 'value' => $property['value'],
    //                 'property_option_id' => $property['property_option_id']
    //             ]);
    //         }
    //     }
    //     //dd($properties_arr);
    //     $product->properties()->sync($properties_arr);
    // }

}
