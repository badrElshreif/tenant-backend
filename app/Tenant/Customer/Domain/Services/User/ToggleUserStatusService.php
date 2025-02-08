<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use App\Infrastructure\Exceptions\UserNotFoundException;
use DB;
use Symfony\Component\HttpFoundation\Response;

class ToggleUserStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            $user = Customer::findOrFail($data['user_id']);
            if($user->is_active == 1){
                $user->statuses()->create([
                    'reason' => $data['reason'],
                    'status' => 'in active',
                ]);
            }
            $user->update([
                'is_active' => !$user->is_active
            ]);
            DB::commit();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
        return new GenericPayload($user, Response::HTTP_CREATED);

    }
}
