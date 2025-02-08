<?php

namespace App\User\Domain\Services\UserAddress;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\UserAddress;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Infrastructure\Exceptions\QueryException;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserAddressService extends Service
{
    public function handle($data = []) 
    {
        try {
            $address = UserAddress::findOrFail($data['address_id']);
            if(count($address->orders()->get()) > 0)
                return new GenericPayload(
                    __('error.cannotDelete'), 422
                );
            
            $address->delete();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
            throw new QueryException;
        }
        return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);

    }
}