<?php

namespace App\User\Domain\Services\UserAddress;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\UserAddress;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ShowUserAddressService extends Service
{
    public function handle($data = []) 
    {
        try {
            $address = UserAddress::findOrFail($data['address_id']);
            return new GenericPayload($address, Response::HTTP_CREATED);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }
    }
}