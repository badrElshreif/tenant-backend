<?php

namespace App\Tenant\Order\Domain\Services\Transaction;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\User\Domain\Models\User;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class CreateTransactionService extends Service
{
    public function handle($data = [])
    {
        try {

            $user = User::findOrFail($data['user_id']);
            $wallet = $user->transactions()->orderBy('id', 'desc')->first();
            $data['wallet_total'] = $wallet ? $wallet->wallet_total : 0.00;

            if($data['type'] == 'pay_in'){
                $data['wallet_total'] = $data['wallet_total'] + $data['amount'];
            }else{
                if($data['wallet_total'] < $data['amount'])
                    return new GenericPayload(
                        __('error.unSufficientWallet'), 422
                    );
                $data['wallet_total'] = $data['wallet_total'] - $data['amount'];
            }

            $data['transactable_id'] = auth()->id();
            $data['transactable_type'] = 'App\Admin\Domain\Models\Admin';
            $transaction = $user->transactions()->create($data);
	        return new GenericPayload($transaction, Response::HTTP_CREATED);

	    }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (\PDOException $ex){
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (\Exception $ex) {
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }

    }
}
