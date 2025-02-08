<?php

namespace App\User\Domain\Services\UserAddress;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\UserAddress;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Infrastructure\Exceptions\QueryException;
use Symfony\Component\HttpFoundation\Response;

class ListUserAddressesService extends Service
{
    public function handle($data = []) 
    {
        try {
            $addresses = auth()->user()->addresses()->get();
            return new GenericPayload($addresses, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
            throw new QueryException;
        }
    }
}