<?php

namespace App\User\Domain\Services\UserAddress;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\UserAddress;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserAddressService extends Service
{
    public function handle($data = []) 
    {
        try {
            $address = UserAddress::findOrFail($data['address_id']);
            if(isset($data['is_primary']) && $data['is_primary'] == 1){
            $address_check = auth()->user()->addresses->where('is_primary', 1)->where('id', '!=', $address->id)->first();
            if($address_check)
                $address_check->update([
                    'is_primary' => 0
                ]);
        }
            $address->update($data);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }
        return new GenericPayload($address, Response::HTTP_CREATED);

    }
}