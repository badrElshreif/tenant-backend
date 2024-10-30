<?php

namespace App\Property\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Property\Domain\Models\Property;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreatePropertyService extends Service
{
    public function handle($data = []) 
    {
        try {
	        // Begin Transaction
	        DB::beginTransaction();
	        $property = Property::create($data);
	        if(isset($data['categories']))
	        	$property->categories()->sync($data['categories'], false);

	        if(isset($data['options'])){
	        	$property->propertyOptions()->createMany($data['options']);
	        }
	        // Commit Transaction
	        DB::commit();
	        return new GenericPayload($property, Response::HTTP_CREATED);
	        
	    }catch (\Illuminate\Database\QueryException $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (\PDOException $ex){
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (\Exception $ex) {
        	// Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

    }
}