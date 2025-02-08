<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use App\User\Domain\Resources\UserResource;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserService extends Service
{
    public function handle($data = [])
    {
        try {
            if(isset($data['phone']) && str_starts_with($data['phone'], '0')){
                $data['phone'] = substr($data['phone'],1,20);
            }

            $user = Customer::findOrFail($data['user_id']);
            $user->update($data);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }
        return new GenericPayload($user, Response::HTTP_CREATED);

    }
}
