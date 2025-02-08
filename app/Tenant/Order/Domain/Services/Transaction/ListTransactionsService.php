<?php

namespace App\Tenant\Order\Domain\Services\Transaction;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Transaction;
use App\User\Domain\Models\Customer;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ListTransactionsService extends Service
{
    public function handle($data = [])
    {
        try {
	        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
	        $user = Customer::findOrFail($data['user_id']);
	        $transactions = $user->transactions()->orderBy('created_at', 'desc')->paginate($limit);

	        return new GenericPayload($transactions, Response::HTTP_ACCEPTED);
	   	}catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        }  catch (\Exception $ex) {
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }
    }

}
