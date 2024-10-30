<?php

namespace App\Property\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Property\Domain\Models\Property;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class TogglePropertyStatusService extends Service
{
    public function handle($data = []) 
    {
        try {
            $property = Property::findOrFail($data['property_id']);
            if($property->is_active == 1){
                if(count($property->products()->get()) > 0)
                    return new GenericPayload(
                         __('error.cannotDeactivate'), 422
                    ); 
            }
            $property->update([
                'is_active' => !$property->is_active
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }
        return new GenericPayload($property, Response::HTTP_CREATED);

    }
}