<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ListUserAddressesService extends Service
{
    public function handle($data = [])
    {
        try {
            $user = Customer::findOrFail($data['user_id']);
            return new GenericPayload($user->addresses()->get(), Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }
}
