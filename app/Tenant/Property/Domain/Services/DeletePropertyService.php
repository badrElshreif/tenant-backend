<?php

namespace App\Property\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Property\Domain\Models\Property;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class DeletePropertyService extends Service
{
    public function handle($data = []) 
    {
        try {
            $property = Property::findOrFail($data['property_id']);
            if(count($property->products()->get()) > 0)
        		return new GenericPayload(
                    __('error.cannotDelete'), 422
                );
            $property->propertyOptions()->delete();
            $property->categories()->detach();
            $property->delete();
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