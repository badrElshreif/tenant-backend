<?php

namespace App\Property\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Property\Domain\Models\Property;
use App\Property\Domain\Models\PropertyOption;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdatePropertyService extends Service
{
    public function handle($data = []) 
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            $property = Property::findOrFail($data['property_id']);
            // if(isset($data['is_active']) && $data['is_active'] == 0){
            // 	if(count($property->products()->get()) > 0)
            // 		return new GenericPayload(
            //              __('error.cannotDeactivate'), 422
            //         ); 
            // }
            $property->update($data);
            if(isset($data['categories']))
                $property->categories()->sync($data['categories']);

            if(isset($data['options']))
                $this->saveOptions($property, $data['options']);

        // Commit Transaction
            DB::commit();
            $property = Property::findOrFail($data['property_id']);
            return new GenericPayload($property, Response::HTTP_CREATED);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }

    private function saveOptions($property, $options=[]){
        $option_ids = array_column($options, 'id');
        foreach($property->propertyOptions as $option){
            if(! in_array($option->id, $option_ids))
                $option->delete();
        }
        foreach($options as $option){
            $property->propertyOptions()->updateOrCreate(
                [
                    'id' => isset($option['id']) ?$option['id'] : null,
                    'property_id' => $property->id
                ],
                $option
            );

            // if( isset($option['id']) && $option['id'] != null ){
            //     $property_option = PropertyOption::whereId($option['id'])->wherePropertyId($property->id)->firstOrFail();
            //     $property_option->update($option);
            // }else{
            //     $property->propertyOptions()->create($option);
            // }
        }
    }
}